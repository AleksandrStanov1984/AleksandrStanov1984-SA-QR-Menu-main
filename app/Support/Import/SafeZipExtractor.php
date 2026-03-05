<?php

namespace App\Support\Import;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class SafeZipExtractor
{
    public const MAX_SIZE_MB = 50;
    public const MAX_FILES = 300;

    protected array $allowedExt = ['jpg','jpeg','png','webp','svg'];
    protected array $blockedExt = ['php','js','exe','bat','cmd','ps1','sh','dll','so','html'];

    public function extract(UploadedFile $zip, int $restaurantId): array
    {
        if ($zip->getSize() > self::MAX_SIZE_MB * 1024 * 1024) {
            throw new \RuntimeException('admin.import.errors.zip_too_large');
        }

        $zipPath = $zip->getRealPath();
        $zipArc = new ZipArchive();

        if ($zipArc->open($zipPath) !== true) {
            throw new \RuntimeException('admin.import.errors.zip_open_failed');
        }

        if ($zipArc->numFiles > self::MAX_FILES) {
            throw new \RuntimeException('admin.import.errors.zip_too_many_files');
        }

        $tmpRoot = storage_path('app/tmp/zip-' . Str::uuid());
        mkdir($tmpRoot, 0755, true);

        $stored = [];

        for ($i = 0; $i < $zipArc->numFiles; $i++) {
            $stat = $zipArc->statIndex($i);
            $name = $stat['name'];

            // directories → skip
            if (str_ends_with($name, '/')) {
                continue;
            }

            // path safety
            if ($this->isUnsafePath($name)) {
                throw new \RuntimeException('admin.import.errors.zip_unsafe_path');
            }

            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

            if (in_array($ext, $this->blockedExt, true)) {
                throw new \RuntimeException('admin.import.errors.zip_blocked_ext');
            }

            if (!in_array($ext, $this->allowedExt, true)) {
                throw new \RuntimeException('admin.import.errors.zip_ext_not_allowed');
            }

            $target = $tmpRoot . '/' . $name;
            @mkdir(dirname($target), 0755, true);

            copy("zip://{$zipPath}#{$name}", $target);

            if ($ext === 'svg') {
                $this->sanitizeSvg($target);
            }

            $stored[] = $name;
        }

        $zipArc->close();

        // move to final storage
        $finalRoot = "restaurants/{$restaurantId}/import";
        foreach ($stored as $rel) {
            Storage::disk('public')->put(
                "{$finalRoot}/{$rel}",
                file_get_contents($tmpRoot . '/' . $rel)
            );
        }

        return [
            'path' => $finalRoot,
            'files' => $stored,
        ];
    }

    private function isUnsafePath(string $path): bool
    {
        return str_contains($path, '..')
            || str_contains($path, '\\')
            || str_starts_with($path, '/');
    }

    /**
     * Минимальный, но надёжный SVG sanitize
     */
    private function sanitizeSvg(string $file): void
    {
        $svg = file_get_contents($file);

        // remove scripts
        $svg = preg_replace('#<script.*?>.*?</script>#is', '', $svg);

        // remove on* handlers
        $svg = preg_replace('/on\w+="[^"]*"/i', '', $svg);

        // remove external hrefs
        $svg = preg_replace('/xlink:href="https?:\/\/[^"]+"/i', '', $svg);

        file_put_contents($file, $svg);
    }
}
