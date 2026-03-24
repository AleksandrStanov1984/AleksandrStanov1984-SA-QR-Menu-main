<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Support\Permissions;
use App\Services\QrService;
use App\Services\ImageService;

class RestaurantQrController extends Controller
{
    public function index(Restaurant $restaurant)
    {
        $restaurant->load('qr');

        return view('admin.restaurants.qr', [
            'restaurant' => $restaurant,
            'qr' => $restaurant->qr,
        ]);
    }

    public function generate(Request $request, Restaurant $restaurant)
    {
        Permissions::abortUnless(auth()->user(), 'restaurants.edit');

        $path = app(QrService::class)->generate(
            $restaurant,
            $request->file('logo'),
            $request->file('background')
        );

        return response()->json([
            'success' => true,
            'image' => app(ImageService::class)->qr($path),
        ]);
    }

    public function download(Restaurant $restaurant, string $format)
    {
        Permissions::abortUnless(auth()->user(), 'restaurants.edit');

        if (!in_array($format, ['svg', 'pdf'], true)) {
            abort(404);
        }

        $qr = $restaurant->qr;

        if (!$qr || !$qr->qr_path) {
            abort(404);
        }

        $svgPath = public_path('assets/' . ltrim($qr->qr_path, '/'));

        if (!File::exists($svgPath)) {
            abort(404);
        }

        $filename = 'qr-' . $restaurant->slug;

        // =========================
        // SVG
        // =========================
        if ($format === 'svg') {
            return response()->download($svgPath, $filename . '.svg');
        }

        // =========================
        // PDF
        // =========================
        if ($format === 'pdf') {

            $tmpDir = storage_path('app/tmp');

            if (!file_exists($tmpDir)) {
                mkdir($tmpDir, 0777, true);
            }

            $pngPath = $tmpDir . '/qr_' . uniqid() . '.png';

            // WINDOWS PATH (ВАЖНО!)
            $inkscape = '"C:\\Program Files\\Inkscape\\bin\\inkscape.exe"';

            $command = $inkscape
                . ' "' . $svgPath . '"'
                . ' --export-type=png'
                . ' --export-area-page'
                . ' --export-background-opacity=1'
                . ' --export-filename="' . $pngPath . '"';

            exec($command);

            if (!file_exists($pngPath)) {
                abort(500, 'SVG → PNG failed (Inkscape)');
            }

            $pngBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($pngPath));

            $html = '
    <div style="text-align:center;">
        <img src="'.$pngBase64.'" style="width:100%; max-width:800px;">
    </div>
    ';

            $pdf = Pdf::loadHTML($html);

            return $pdf->download($filename . '.pdf');
        }

        abort(404);
    }
}
