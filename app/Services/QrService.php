<?php

namespace App\Services;

use App\Models\Restaurant;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrService
{
    public function __construct(
        protected ImagePipelineService $pipeline
    ) {}

    public function generate(
        Restaurant $restaurant,
        ?UploadedFile $logo = null,
        ?UploadedFile $background = null
    ): string {
        $token = $restaurant->token()->firstOrCreate(
            ['restaurant_id' => $restaurant->id],
            ['token' => Str::random(10)]
        );

        $url = route('qr.resolve', ['token' => $token->token]);

        $base = "restaurants/{$restaurant->id}/qr";

        $qrRecord = $restaurant->qr()->firstOrCreate(
            ['restaurant_id' => $restaurant->id],
            [
                'qr_path' => null,
                'logo_path' => null,
                'background_path' => null,
                'settings' => [],
            ]
        );

        $logoPath = $qrRecord->logo_path;
        $backgroundPath = $qrRecord->background_path;

        if ($logo) {
            $logoPath = $this->storePublicAsset($logo, "{$base}/logo");
        }

        if ($background) {
            $backgroundPath = $this->storePublicAsset($background, "{$base}/background");
        }

        // raw SVG -> qr/raw
        $rawSvg = QrCode::format('svg')
            ->size(900)
            ->margin(1)
            ->generate($url);

        $rawName = 'qr_raw_' . Str::uuid() . '.svg';
        $rawDir = public_path("assets/{$base}/raw");

        if (!File::exists($rawDir)) {
            File::makeDirectory($rawDir, 0755, true);
        }

        $rawFullPath = $rawDir . DIRECTORY_SEPARATOR . $rawName;
        File::put($rawFullPath, $rawSvg);

        $rawPath = "{$base}/raw/{$rawName}";

        // final SVG -> qr/final
        $finalSvg = $this->buildFinalSvg(
            restaurant: $restaurant,
            rawSvgAbsolutePath: $rawFullPath,
            logoAbsolutePath: $logoPath ? public_path('assets/' . ltrim($logoPath, '/')) : null,
            backgroundAbsolutePath: $backgroundPath ? public_path('assets/' . ltrim($backgroundPath, '/')) : null,
        );

        $finalName = 'qr_final_' . Str::uuid() . '.svg';
        $finalDir = public_path("assets/{$base}/final");

        if (!File::exists($finalDir)) {
            File::makeDirectory($finalDir, 0755, true);
        }

        $finalFullPath = $finalDir . DIRECTORY_SEPARATOR . $finalName;
        File::put($finalFullPath, $finalSvg);

        $finalPath = "{$base}/final/{$finalName}";

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

    private function buildFinalSvg(
        Restaurant $restaurant,
        string $rawSvgAbsolutePath,
        ?string $logoAbsolutePath = null,
        ?string $backgroundAbsolutePath = null
    ): string {

        $w = 1200;
        $h = 1820;

        // =========================
        // LOGO
        // =========================
        $logoSize = 420;
        $logoX = ($w - $logoSize) / 2;
        $logoY = 90;
        $logoRadius = $logoSize / 2;

        $logoBottom = $logoY + $logoSize;

        // =========================
        // TITLE
        // =========================
        $titleY = $logoBottom + 80;

        // =========================
        // QR BLOCK
        // =========================
        $qrSize = 750;
        $qrX = ($w - $qrSize) / 2;
        $qrY = $titleY + 60;

        $qrInnerOffset = 40;
        $qrImageX = $qrX + $qrInnerOffset;
        $qrImageY = $qrY + $qrInnerOffset;
        $qrImageSize = $qrSize - ($qrInnerOffset * 2);

        $qrBottom = $qrY + $qrSize;

        // =========================
        // CONTACTS
        // =========================
        $phoneY = $qrBottom + 90;
        $addressY = $phoneY + 70;
        $emailY = $addressY + 70;
        $scanY = $emailY + 80;

        // =========================
        // DATA
        // =========================
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

        // =========================
        // BACKGROUND
        // =========================
        $bg = '<rect width="100%" height="100%" fill="#0f172a"/>';

        if ($backgroundAbsolutePath && File::exists($backgroundAbsolutePath)) {
            $bgData = $this->fileToDataUri($backgroundAbsolutePath);

            $bg = <<<SVG
<image href="{$bgData}" x="0" y="0" width="{$w}" height="{$h}" preserveAspectRatio="xMidYMid slice"/>
<rect width="100%" height="100%" fill="#000000" opacity="0.45"/>
SVG;
        }

        // =========================
        // LOGO (CIRCLE)
        // =========================
        $logo = '';

        if ($logoAbsolutePath && File::exists($logoAbsolutePath)) {
            $logoData = $this->fileToDataUri($logoAbsolutePath);

            $logo = <<<SVG
<defs>
    <clipPath id="logoClip">
        <circle cx="{$logoRadius}" cy="{$logoRadius}" r="{$logoRadius}"/>
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

        // =========================
        // QR
        // =========================
        $qrData = $this->fileToDataUri($rawSvgAbsolutePath);

        $qr = <<<SVG
<rect x="{$qrX}" y="{$qrY}"
      width="{$qrSize}"
      height="{$qrSize}"
      rx="40"
      fill="#ffffff"/>

<image href="{$qrData}"
       x="{$qrImageX}"
       y="{$qrImageY}"
       width="{$qrImageSize}"
       height="{$qrImageSize}"/>
SVG;

        // =========================
        // TEXTS
        // =========================
        $titleBlock = <<<SVG
<text x="600"
      y="{$titleY}"
      text-anchor="middle"
      font-size="78"
      font-weight="800"
      fill="white">
    {$title}
</text>
SVG;

        $phoneBlock = $phone !== ''
            ? '<text x="600" y="'.$phoneY.'" text-anchor="middle" font-size="54" fill="'.$brand.'" font-weight="700">'.$phone.'</text>'
            : '';

        $addressBlock = $address !== ''
            ? '<text x="600" y="'.$addressY.'" text-anchor="middle" font-size="40" fill="#e5e7eb">'.$address.'</text>'
            : '';

        $emailBlock = $email !== ''
            ? '<text x="600" y="'.$emailY.'" text-anchor="middle" font-size="40" fill="#e5e7eb">'.$email.'</text>'
            : '';

        $scanBlock = <<<SVG
<text x="600"
      y="{$scanY}"
      text-anchor="middle"
      font-size="66"
      fill="#ffffff"
      opacity="0.8">
    Scan to view menu
</text>
SVG;

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$w}" height="{$h}">

    {$bg}

    {$logo}

    {$titleBlock}

    {$qr}

    {$phoneBlock}
    {$addressBlock}
    {$emailBlock}

    {$scanBlock}

</svg>
SVG;
    }

    private function storePublicAsset(UploadedFile $file, string $relativeDir): string
    {
        $ext = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'png');
        $name = (string) Str::uuid() . '.' . $ext;

        $dir = public_path('assets/' . trim($relativeDir, '/'));
        if (!File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $target = $dir . DIRECTORY_SEPARATOR . $name;
        File::copy($file->getRealPath(), $target);

        return trim($relativeDir, '/') . '/' . $name;
    }

    private function fileToDataUri(string $absolutePath): ?string
    {
        if (!File::exists($absolutePath)) {
            return null;
        }

        $mime = File::mimeType($absolutePath);
        $data = base64_encode(File::get($absolutePath));

        return "data:{$mime};base64,{$data}";
    }

    private function escapeXml(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    private function hexToRgba(string $hex, float $alpha): string
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) !== 6) {
            return "rgba(17,17,17,{$alpha})";
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return "rgba({$r},{$g},{$b},{$alpha})";
    }
}
