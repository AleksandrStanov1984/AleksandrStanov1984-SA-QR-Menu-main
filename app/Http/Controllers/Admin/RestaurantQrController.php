<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\TenantAccessException;
use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Support\Permissions;
use App\Services\QrService;
use App\Services\ImageService;
use App\Support\Guards\AccessGuardTrait;

class RestaurantQrController extends Controller
{
    use AccessGuardTrait;

    /**
     * @throws TenantAccessException
     */
    public function index(Request $request, Restaurant $restaurant)
    {
        $this->assertRestaurantAccess($request, $restaurant);

        $restaurant->load('qr');

        $menuUrl = route('restaurant.show', $restaurant->slug);

        return view('admin.restaurants.qr', [
            'restaurant' => $restaurant,
            'qr' => $restaurant->qr,
            'menuUrl' => $menuUrl,
        ]);
    }

    /**
     * @throws TenantAccessException
     */
    public function generate(Request $request, Restaurant $restaurant)
    {
        $this->assertRestaurantAccess($request, $restaurant);

        if ($resp = Permissions::denyRedirect($request->user(), 'restaurants.edit')) {
            return $resp;
        }

        $restaurant->unsetRelation('qr');
        $restaurant->load('qr');

        $path = app(QrService::class)->generate(
            $restaurant,
            $request->file('logo'),
            $request->file('background')
        );

        return response()->json([
            'status' => true,
            'image'  => app(ImageService::class)->qr($path),
        ]);
    }

    /**
     * @throws TenantAccessException
     */
    public function download(Request $request, Restaurant $restaurant, string $format)
    {
        $this->assertRestaurantAccess($request, $restaurant);

        if ($resp = Permissions::denyRedirect($request->user(), 'restaurants.edit')) {
            return $resp;
        }

        if (!in_array($format, ['svg', 'pdf'], true)) {
            throw new TenantAccessException(__('admin.errors.invalid_format'));
        }

        $qr = $restaurant->qr;

        if (!$qr || !$qr->qr_path) {
            throw new TenantAccessException(__('admin.errors.qr_not_found'));
        }

        $imageService = app(ImageService::class);

        $svgPath = $imageService->path($qr->qr_path);

        if (!$imageService->existsPublic($qr->qr_path)) {
            throw new TenantAccessException(__('admin.errors.file_not_found'));
        }

        $filename = 'qr-' . $restaurant->slug;

        // SVG
        if ($format === 'svg') {
            return response()->download($svgPath, $filename . '.svg');
        }

        // PDF
        $tmpDir = storage_path('app/tmp');

        if (!file_exists($tmpDir)) {
            mkdir($tmpDir, 0777, true);
        }

        $pngPath = $tmpDir . '/qr_' . uniqid() . '.png';

        $inkscape = '"C:\\Program Files\\Inkscape\\bin\\inkscape.exe"';

        $command = $inkscape
            . ' "' . $svgPath . '"'
            . ' --export-type=png'
            . ' --export-area-page'
            . ' --export-background-opacity=1'
            . ' --export-filename="' . $pngPath . '"';

        try {

            exec($command, $output, $code);

            if ($code !== 0 || !file_exists($pngPath)) {
                throw new TenantAccessException(__('admin.errors.qr_convert_failed'));
            }

            $pngBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($pngPath));

            $html = '
                <div style="text-align:center;">
                    <img src="'.$pngBase64.'" style="width:100%; max-width:800px;">
                </div>
            ';

            $pdf = Pdf::loadHTML($html);

            return $pdf->download($filename . '.pdf');

        } finally {
            if (file_exists($pngPath)) {
                @unlink($pngPath);
            }
        }
    }

    /**
     * @throws TenantAccessException
     */
    public function deleteLogo(Request $request, Restaurant $restaurant)
    {
        $this->assertRestaurantAccess($request, $restaurant);

        $qr = $restaurant->qr;

        if ($qr?->logo_path) {

            $fullPath = public_path($qr->logo_path);

            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }

            $qr->update(['logo_path' => null]);
        }

        $restaurant->unsetRelation('qr');
        $restaurant->load('qr');

        app(QrService::class)->generate($restaurant, null, null);

        return response()->json(['success' => true]);
    }

    /**
     * @throws TenantAccessException
     */
    public function deleteBackground(Request $request, Restaurant $restaurant)
    {
        $this->assertRestaurantAccess($request, $restaurant);

        $qr = $restaurant->qr;

        if ($qr?->background_path) {

            $fullPath = public_path($qr->background_path);

            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }

            $qr->update(['background_path' => null]);
        }

        $restaurant->unsetRelation('qr');
        $restaurant->load('qr');

        app(QrService::class)->generate($restaurant, null, null);

        return response()->json(['success' => true]);
    }
}
