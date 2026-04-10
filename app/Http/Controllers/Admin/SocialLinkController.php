<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\TenantAccessException;

use App\Http\Controllers\Controller;

use App\Models\Restaurant;
use App\Models\RestaurantSocialLink;

use App\Services\ImageService;

use App\Support\Guards\AccessGuardTrait;

use Illuminate\Http\Request;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Throwable;

class SocialLinkController extends Controller
{
    use AccessGuardTrait;

    public function index(Restaurant $restaurant)
    {
        $linksArr = $restaurant->socialLinks()
        ->select([
            'id',
            'title',
            'url',
            'icon_path',
            'sort_order',
            'is_active',
            'deleted_at',
        ])
            ->orderBy('sort_order')
            ->get();

        return view('admin.restaurants.socials', [
            'restaurant' => $restaurant,
            'linksArr' => $linksArr,
        ]);
    }

    /**
     * @throws TenantAccessException
     */
    private function assertLinkBelongs(Restaurant $restaurant, RestaurantSocialLink $link): void
    {
        if ((int)$link->restaurant_id !== (int)$restaurant->id) {
            throw new TenantAccessException(__('permissions.no_access'));
        }
    }

    private function sanitizeTitle(string $title): string
    {
        $title = strip_tags($title);
        $title = preg_replace('/\s+/u', ' ', $title);
        return trim($title);
    }

    /**
     * @throws TenantAccessException
     */
    private function validateSafeUrl(string $url): string
    {
        $url = trim($url);

        if (Str::contains($url, ['<', '>', '"', "'", 'javascript:', 'data:', 'vbscript:', 'file:'], true)) {
            throw new TenantAccessException('Invalid URL');
        }

        $parts = parse_url($url);
        if (!$parts || empty($parts['scheme']) || empty($parts['host'])) {
            throw new TenantAccessException('Invalid URL');
        }

        $scheme = strtolower((string)$parts['scheme']);
        if (!in_array($scheme, ['http', 'https'], true)) {
            throw new TenantAccessException('Invalid URL');
        }

        return $url;
    }

    /**
     * @throws TenantAccessException
     */
    private function sanitizeSvgUpload(Request $request, string $field): void
    {
        $file = $request->file($field);
        if (!$file) return;

        $content = @file_get_contents($file->getRealPath());
        if (!is_string($content) || $content === '') {
            throw new TenantAccessException('Invalid SVG');
        }

        $lc = strtolower($content);

        if (Str::contains($lc, ['<script', 'onload=', 'onerror=', 'javascript:'], true)) {
            throw new TenantAccessException('Unsafe SVG');
        }
    }

    /**
     *  Получаем лимит по плану
     */
    private function getLimit(Restaurant $restaurant): int
    {
        return (int) $restaurant->feature('social_limit', 1);
    }

    /**
     * @throws TenantAccessException
     */
    public function store(Request $request, Restaurant $restaurant)
    {
        $this->assertRestaurantAccess($request, $restaurant);

        if (!$restaurant->feature('social_links')) {
            throw new TenantAccessException(__('permissions.no_access'));
        }

        $limit = $this->getLimit($restaurant);

        $activeCount = $restaurant->socialLinks()
            ->where('is_active', true)
            ->count();

        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'url'   => ['required', 'string', 'url', 'max:2048'],
            'icon'  => ['nullable', 'file', 'mimetypes:image/svg+xml', 'max:256'],
        ]);

        $title = $this->sanitizeTitle((string)$data['title']);
        $url   = $this->validateSafeUrl((string)$data['url']);

        if ($request->hasFile('icon')) {
            $this->sanitizeSvgUpload($request, 'icon');
        }

        $nextSort = (int) $restaurant->socialLinks()->max('sort_order');
        $nextSort = $nextSort ? $nextSort + 1 : 1;

        $iconPath = null;

        if ($request->hasFile('icon')) {
            $iconPath = app(\App\Services\ImagePipelineService::class)
                ->uploadSvg(
                    $request->file('icon'),
                    "restaurants/{$restaurant->id}/social"
                );
        }

        $isActive = $activeCount < $limit;

        RestaurantSocialLink::create([
            'restaurant_id' => $restaurant->id,
            'title' => $title,
            'url' => $url,
            'icon_path' => $iconPath,
            'sort_order' => $nextSort,
            'is_active' => $isActive,
        ]);

        return back()->with('status', __('admin.socials.saved'));
    }

    /**
     * @throws TenantAccessException
     */
    public function update(Request $request, Restaurant $restaurant, RestaurantSocialLink $link)
    {
        $this->assertRestaurantAccess($request, $restaurant);
        $this->assertLinkBelongs($restaurant, $link);

        if (!$restaurant->feature('social_links')) {
            throw new TenantAccessException(__('permissions.no_access'));
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'url'   => ['required', 'string', 'url', 'max:2048'],
            'icon'  => ['nullable', 'file', 'mimetypes:image/svg+xml', 'max:256'],
            'remove_icon' => ['nullable', 'boolean'],
        ]);

        $title = $this->sanitizeTitle((string)$data['title']);
        $url   = $this->validateSafeUrl((string)$data['url']);

        if ($request->hasFile('icon')) {
            $this->sanitizeSvgUpload($request, 'icon');
        }

        if (!empty($data['remove_icon']) && !empty($link->icon_path)) {
            app(ImageService::class)->delete($link->icon_path);
            $link->icon_path = null;
        }

        if ($request->hasFile('icon')) {
            if (!empty($link->icon_path)) {
                app(ImageService::class)->delete($link->icon_path);
            }

            $link->icon_path = app(\App\Services\ImagePipelineService::class)
                ->uploadSvg(
                    $request->file('icon'),
                    "restaurants/{$restaurant->id}/social"
                );
        }

        $link->update([
            'title' => $title,
            'url' => $url,
        ]);

        return back()->with('status', __('admin.socials.updated_ok'));
    }

    /**
     * @throws TenantAccessException
     */
    public function destroy(Request $request, Restaurant $restaurant, RestaurantSocialLink $link)
    {
        $this->assertRestaurantAccess($request, $restaurant);
        $this->assertLinkBelongs($restaurant, $link);

        if (!$restaurant->feature('social_links')) {
            throw new TenantAccessException(__('permissions.no_access'));
        }

        if (!empty($link->icon_path)) {
            app(ImageService::class)->delete($link->icon_path);
        }

        $link->update([
            'deleted_by_user_id' => $request->user()?->id
        ]);

        $link->delete();

        return back()->with('status', __('admin.socials.deleted_ok'));
    }

    /**
     * @throws TenantAccessException
     * @throws Throwable
     */
    public function toggleActive(Request $request, Restaurant $restaurant, RestaurantSocialLink $link)
    {
        $this->assertRestaurantAccess($request, $restaurant);
        $this->assertLinkBelongs($restaurant, $link);

        if (!$restaurant->feature('social_links')) {
            throw new TenantAccessException(__('permissions.no_access'));
        }

        $limit = $this->getLimit($restaurant);

        DB::transaction(function () use ($restaurant, $link, $limit) {

            $activeLinks = $restaurant->socialLinks()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->lockForUpdate()
                ->get();

            if (!$link->is_active) {
                if ($activeLinks->count() >= $limit) {
                    $last = $activeLinks->last();

                    if ($last && $last->id !== $link->id) {
                        $last->update([
                            'is_active' => false
                        ]);
                    }
                }

                $link->update([
                    'is_active' => true
                ]);

            } else {
                $link->update([
                    'is_active' => false
                ]);
            }
        });

        return back()->with('status', __('admin.common.saving'));
    }

    /**
     * @throws TenantAccessException
     */
    public function reorder(Request $request, Restaurant $restaurant)
    {
        $this->assertRestaurantAccess($request, $restaurant);

        $items = $request->input('items', []);

        foreach ($items as $item) {
            \App\Models\RestaurantSocialLink::where('id', $item['id'])
                ->where('restaurant_id', $restaurant->id)
                ->update([
                    'sort_order' => (int)$item['sort_order']
                ]);
        }

        // пересчёт active по лимиту
        $limit = (int) $restaurant->feature('social_limit', 1);

        $links = $restaurant->socialLinks()
            ->orderBy('sort_order')
            ->get();

        foreach ($links as $index => $link) {
            $link->update([
                'is_active' => $index < $limit
            ]);
        }

        return response()->json(['ok' => true]);
    }
}
