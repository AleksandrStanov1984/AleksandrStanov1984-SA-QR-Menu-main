<?php

namespace App\Services;

use App\Models\Restaurant;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrService
{
    public function generate(
        Restaurant $restaurant,
        ?UploadedFile $logo = null,
        ?UploadedFile $background = null
    ): string {

        // =========================
        // TOKEN
        // =========================
        $token = $restaurant->token()->firstOrCreate(
            ['restaurant_id' => $restaurant->id],
            ['token' => Str::random(10)]
        );

        $url = route('qr.resolve', ['token' => $token->token]);

        $base = "restaurants/{$restaurant->id}/qr";

        // =========================
        // QR RECORD
        // =========================
        $qrRecord = $restaurant->qr()->firstOrCreate(
            ['restaurant_id' => $restaurant->id],
            [
                'qr_path' => null,
                'logo_path' => null,
                'background_path' => null,
                'settings' => [],
            ]
        );

        // =========================
        // RAW
        // =========================
        $rawPath = $qrRecord->settings['raw_path'] ?? null;

        if (!$rawPath || !File::exists(public_path('assets/'.$rawPath))) {

            $rawSvg = QrCode::format('svg')
                ->size(900)
                ->margin(1)
                ->generate($url);

            $rawName = 'qr_raw_' . Str::uuid() . '.svg';
            $rawDir = public_path("assets/{$base}/raw");

            File::ensureDirectoryExists($rawDir);

            $rawFullPath = $rawDir . '/' . $rawName;
            File::put($rawFullPath, $rawSvg);

            $rawPath = "{$base}/raw/{$rawName}";
        }

        $rawFullPath = public_path('assets/'.$rawPath);

        // =========================
        // LOGO
        // =========================
        if ($logo) {

            // удаляем старый QR logo
            $this->deleteQrFile($qrRecord->logo_path, $restaurant->id);

            $logoPath = $this->storePublicAsset($logo, "{$base}/logo");

        } elseif (!empty($restaurant->logo_path)) {

            // если НЕ загрузили — чистим QR logo
            $this->deleteQrFile($qrRecord->logo_path, $restaurant->id);

            // используем базовый restaurant logo
            $logoPath = $this->normalizeRelative($restaurant->logo_path);

        } else {

            // вообще нет — тоже чистим QR logo
            $this->deleteQrFile($qrRecord->logo_path, $restaurant->id);

            $logoPath = null;
        }

        // =========================
        // BACKGROUND
        // =========================
        if ($background) {

            $this->deleteQrFile($qrRecord->background_path, $restaurant->id);

            $backgroundPath = $this->storePublicAsset($background, "{$base}/background");

        } elseif (!empty($restaurant->background_path)) {

            // чистим QR background
            $this->deleteQrFile($qrRecord->background_path, $restaurant->id);

            $backgroundPath = $this->normalizeRelative($restaurant->background_path);

        } else {

            // чистим QR background
            $this->deleteQrFile($qrRecord->background_path, $restaurant->id);

            $backgroundPath = null;
        }

        // =========================
        // FINAL
        // =========================
        $this->deleteQrFile($qrRecord->qr_path, $restaurant->id);

        $finalSvg = $this->buildFinalSvg(
            restaurant: $restaurant,
            rawSvgAbsolutePath: $rawFullPath,
            logoAbsolutePath: $logoPath ? public_path('assets/'.$logoPath) : null,
            backgroundAbsolutePath: $backgroundPath ? public_path('assets/'.$backgroundPath) : null,
        );

        $finalName = 'qr_final_' . Str::uuid() . '.svg';
        $finalDir = public_path("assets/{$base}/final");

        File::ensureDirectoryExists($finalDir);

        $finalFullPath = $finalDir . '/' . $finalName;
        File::put($finalFullPath, $finalSvg);

        $finalPath = "{$base}/final/{$finalName}";

        // =========================
        // SAVE
        // =========================
        $settings = is_array($qrRecord->settings) ? $qrRecord->settings : [];
        $settings['raw_path'] = $rawPath;
        $settings['format'] = 'svg-card-v2';

        $restaurant->qr()->updateOrCreate(
            ['restaurant_id' => $restaurant->id],
            [
                'qr_path' => $finalPath,
                'logo_path' => $logoPath,
                'background_path' => $backgroundPath,
                'settings' => $settings,
            ]
        );

        return $finalPath;
    }

    // =========================
    // SVG BUILDER
    // =========================
    private function buildFinalSvg(
        Restaurant $restaurant,
        string $rawSvgAbsolutePath,
        ?string $logoAbsolutePath = null,
        ?string $backgroundAbsolutePath = null
    ): string {

        $w = 1200;
        $h = 1820;

        $hasBackground = $backgroundAbsolutePath && File::exists($backgroundAbsolutePath);
        $hasLogo = $logoAbsolutePath && File::exists($logoAbsolutePath);

        $logoSize = 420;
        $logoX = ($w - $logoSize) / 2;
        $logoY = 90;
        $logoRadius = $logoSize / 2;

        $logoBottom = $hasLogo ? ($logoY + $logoSize) : 90;
        $titleY = $logoBottom + 80;

        $qrSize = 750;
        $qrX = ($w - $qrSize) / 2;
        $qrY = $titleY + 60;

        $qrInnerOffset = 40;
        $qrImageX = $qrX + $qrInnerOffset;
        $qrImageY = $qrY + $qrInnerOffset;
        $qrImageSize = $qrSize - ($qrInnerOffset * 2);

        $qrBottom = $qrY + $qrSize;

        $phoneY = $qrBottom + 90;
        $addressY = $phoneY + 70;
        $emailY = $addressY + 70;
        $scanY = $emailY + 80;

        $title = $this->escapeXml($restaurant->name ?? '');
        $phone = $this->escapeXml($restaurant->phone ?? '');
        $email = $this->escapeXml($restaurant->contact_email ?? '');

        $address = trim(implode(', ', array_filter([
            trim(($restaurant->street ?? '') . ' ' . ($restaurant->house_number ?? '')),
            trim(($restaurant->postal_code ?? '') . ' ' . ($restaurant->city ?? '')),
        ])));

        $address = $this->escapeXml($address);

        $theme = is_array($restaurant->theme_tokens) ? $restaurant->theme_tokens : [];
        $brand = $theme['primary'] ?? '#111111';

        $bg = '<rect width="100%" height="100%" fill="#ffffff"/>';

        if ($hasBackground) {
            $bgData = $this->fileToDataUri($backgroundAbsolutePath);

            $bg = <<<SVG
<image href="{$bgData}" x="0" y="0" width="{$w}" height="{$h}" preserveAspectRatio="xMidYMid slice"/>
<rect width="100%" height="100%" fill="#000000" opacity="0.45"/>
SVG;
        }

        // КРУГЛЫЙ ЛОГО
        $logo = '';

        if ($hasLogo) {
            $logoData = $this->fileToDataUri($logoAbsolutePath);

            $logo = <<<SVG
<defs>
    <clipPath id="logoClip">
        <circle cx="{$logoRadius}" cy="{$logoRadius}" r="{$logoRadius}" />
    </clipPath>
</defs>

<g transform="translate({$logoX}, {$logoY})">
    <image href="{$logoData}"
           width="{$logoSize}"
           height="{$logoSize}"
           clip-path="url(#logoClip)"
           preserveAspectRatio="xMidYMid slice"/>
</g>
SVG;
        }

        $qrData = $this->fileToDataUri($rawSvgAbsolutePath);

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$w}" height="{$h}">
    {$bg}
    {$logo}

    <text x="600" y="{$titleY}" text-anchor="middle" font-size="78" font-weight="800">
        {$title}
    </text>

    <rect x="{$qrX}" y="{$qrY}" width="{$qrSize}" height="{$qrSize}" rx="40" fill="#ffffff"/>

    <image href="{$qrData}" x="{$qrImageX}" y="{$qrImageY}" width="{$qrImageSize}" height="{$qrImageSize}"/>

    <text x="600" y="{$phoneY}" text-anchor="middle" font-size="54" fill="{$brand}">
        {$phone}
    </text>

    <text x="600" y="{$addressY}" text-anchor="middle" font-size="40">
        {$address}
    </text>

    <text x="600" y="{$emailY}" text-anchor="middle" font-size="40">
        {$email}
    </text>

    <text x="600" y="{$scanY}" text-anchor="middle" font-size="66">
        Scan to view menu
    </text>
</svg>
SVG;
    }

    // =========================
    // HELPERS
    // =========================

    private function deleteQrFile(?string $path, int $restaurantId): void
    {
        if (!$path) return;

        $path = ltrim($path, '/');

        if (!str_contains($path, "restaurants/{$restaurantId}/qr")) {
            return;
        }

        $full = public_path('assets/'.$path);

        if (File::exists($full)) {
            File::delete($full);
        }
    }

    private function normalizeRelative(?string $path): ?string
    {
        if (!$path) return null;

        $path = ltrim($path, '/');

        if (str_starts_with($path, 'assets/')) {
            return substr($path, 7);
        }

        return $path;
    }

    private function storePublicAsset(UploadedFile $file, string $relativeDir): string
    {
        $ext = strtolower($file->getClientOriginalExtension() ?: 'png');
        $name = Str::uuid().'.'.$ext;

        $dir = public_path('assets/'.$relativeDir);

        File::ensureDirectoryExists($dir);

        File::copy($file->getRealPath(), $dir.'/'.$name);

        return $relativeDir.'/'.$name;
    }

    private function fileToDataUri(string $path): ?string
    {
        if (!File::exists($path)) return null;

        return 'data:'.File::mimeType($path).';base64,'.base64_encode(File::get($path));
    }

    private function escapeXml(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }
}
