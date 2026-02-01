<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\RestaurantSocialLink;
use App\Support\Permissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SocialLinkController extends Controller
{
    private function assertRestaurantScope(Request $request, Restaurant $restaurant): void
    {
        $user = $request->user();
        if (!$user) abort(403);

        // обычный пользователь только свой ресторан
        if (!$user->is_super_admin && (int)$user->restaurant_id !== (int)$restaurant->id) {
            abort(403);
        }
    }

    private function assertLinkBelongs(Restaurant $restaurant, RestaurantSocialLink $link): void
    {
        abort_unless((int)$link->restaurant_id === (int)$restaurant->id, 404);
    }

    private function sanitizeTitle(string $title): string
    {
        $title = strip_tags($title);
        $title = preg_replace('/\s+/u', ' ', $title);
        return trim($title);
    }

    private function validateSafeUrl(string $url): string
    {
        $url = trim($url);

        // быстрое отсечение XSS мусора
        if (Str::contains($url, ['<', '>', '"', "'", 'javascript:', 'data:', 'vbscript:', 'file:'], true)) {
            abort(422, 'Invalid URL');
        }

        // Laravel validator "url" уже должен быть применён, но дополнительно:
        $parts = parse_url($url);
        if (!$parts || empty($parts['scheme']) || empty($parts['host'])) {
            abort(422, 'Invalid URL');
        }

        $scheme = strtolower((string)$parts['scheme']);
        if (!in_array($scheme, ['http', 'https'], true)) {
            abort(422, 'Invalid URL');
        }

        return $url;
    }

    private function sanitizeSvgUpload(Request $request, string $field): void
    {
        // простая проверка содержимого svg на явный скрипт/handler
        $file = $request->file($field);
        if (!$file) return;

        $content = @file_get_contents($file->getRealPath());
        if (!is_string($content) || $content === '') {
            abort(422, 'Invalid SVG');
        }

        $lc = strtolower($content);

        // запрет script, onload/onerror и javascript:
        if (Str::contains($lc, ['<script', 'onload=', 'onerror=', 'javascript:'], true)) {
            abort(422, 'Unsafe SVG');
        }
    }

    private function canAddCount(Request $request, Restaurant $restaurant, int $currentCount): bool
    {
        $user = $request->user();

        // max 5
        if ($currentCount >= 5) return false;

        // первые две доступны всем (без прав)
        if ($currentCount < 2) return true;

        // 3-я, 4-я, 5-я — по правам
        if ($currentCount === 2) return Permissions::can($user, 'socials.add.3');
        if ($currentCount === 3) return Permissions::can($user, 'socials.add.4');
        if ($currentCount === 4) return Permissions::can($user, 'socials.add.5');

        return false;
    }

    public function store(Request $request, Restaurant $restaurant)
    {
        $this->assertRestaurantScope($request, $restaurant);

        // сколько живых (не удалённых)
        $currentCount = RestaurantSocialLink::query()
            ->where('restaurant_id', $restaurant->id)
            ->whereNull('deleted_at')
            ->count();

        abort_unless($this->canAddCount($request, $restaurant, $currentCount), 403);

        // права на загрузку svg (если файл есть)
        if ($request->hasFile('icon')) {
            Permissions::abortUnless($request->user(), 'socials.icon.upload');
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'url'   => ['required', 'string', 'url', 'max:2048'],
            'icon'  => ['nullable', 'file', 'mimetypes:image/svg+xml', 'max:256'], // 256 KB
        ]);

        $title = $this->sanitizeTitle((string)$data['title']);
        $url   = $this->validateSafeUrl((string)$data['url']);

        if ($request->hasFile('icon')) {
            $this->sanitizeSvgUpload($request, 'icon');
        }

        $nextSort = (int) RestaurantSocialLink::where('restaurant_id', $restaurant->id)->max('sort_order');
        $nextSort = $nextSort ? $nextSort + 1 : 1;

        $iconPath = null;
        if ($request->hasFile('icon')) {
            $iconPath = $request->file('icon')->store("restaurants/{$restaurant->id}/socials", 'public');
        }

        RestaurantSocialLink::create([
            'restaurant_id' => $restaurant->id,
            'title' => $title,
            'url' => $url,
            'icon_path' => $iconPath,
            'sort_order' => $nextSort,
            'is_active' => true,
        ]);

        return back()->with('status', __('admin.socials.saved'));
    }

    public function update(Request $request, Restaurant $restaurant, RestaurantSocialLink $link)
    {
        $this->assertRestaurantScope($request, $restaurant);
        $this->assertLinkBelongs($restaurant, $link);

        // если неактивный — запрещаем всё, кроме toggleActive
        if (!$link->is_active) {
            abort(403);
        }

        Permissions::abortUnless($request->user(), 'socials.edit');

        if ($request->hasFile('icon')) {
            Permissions::abortUnless($request->user(), 'socials.icon.upload');
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

        // remove icon
        if (!empty($data['remove_icon']) && !empty($link->icon_path)) {
            Storage::disk('public')->delete($link->icon_path);
            $link->icon_path = null;
        }

        // upload new icon
        if ($request->hasFile('icon')) {
            if (!empty($link->icon_path)) {
                Storage::disk('public')->delete($link->icon_path);
            }
            $link->icon_path = $request->file('icon')->store("restaurants/{$restaurant->id}/socials", 'public');
        }

        $link->title = $title;
        $link->url = $url;
        $link->save();

        return back()->with('status', __('admin.socials.updated_ok'));
    }

    public function destroy(Request $request, Restaurant $restaurant, RestaurantSocialLink $link)
    {
        $this->assertRestaurantScope($request, $restaurant);
        $this->assertLinkBelongs($restaurant, $link);

        // если неактивный — запрещаем всё, кроме toggleActive
        if (!$link->is_active) {
            abort(403);
        }

        Permissions::abortUnless($request->user(), 'socials.delete');

        $link->deleted_by_user_id = $request->user()?->id;
        $link->save();

        $link->delete();

        return back()->with('status', __('admin.socials.deleted_ok'));
    }

    public function toggleActive(Request $request, Restaurant $restaurant, RestaurantSocialLink $link)
    {
        $this->assertRestaurantScope($request, $restaurant);
        $this->assertLinkBelongs($restaurant, $link);

        Permissions::abortUnless($request->user(), 'socials.toggle.active');

        $link->is_active = !$link->is_active;
        $link->save();

        return back()->with('status', __('admin.common.saved') ?? 'Saved');
    }
}
