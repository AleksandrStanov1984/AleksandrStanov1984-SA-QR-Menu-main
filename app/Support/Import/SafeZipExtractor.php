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
    public const MAX_FILE_SIZE = 5_000_000;

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
        $mapping = [];

        for ($i = 0; $i < $zipArc->numFiles; $i++) {

            $stat = $zipArc->statIndex($i);
            $name = $stat['name'];

            if (str_ends_with($name, '/')) continue;
            if ($this->isUnsafePath($name)) continue;

            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

            if (in_array($ext, $this->blockedExt, true)) continue;
            if (!in_array($ext, $this->allowedExt, true)) continue;
            if (($stat['size'] ?? 0) > self::MAX_FILE_SIZE) continue;

            $originalName = $this->normalizePath($name);
            $originalKey = $this->normalizeKey(pathinfo($originalName, PATHINFO_FILENAME));

            $storedBase = (string) Str::uuid();
            $storedName = $storedBase . '.' . $ext;

            $target = $tmpRoot . '/' . $storedName;

            try {
                copy("zip://{$zipPath}#{$stat['name']}", $target);
            } catch (\Throwable $e) {
                continue;
            }

            if (!file_exists($target)) continue;

            $mime = @mime_content_type($target);
            if (!$mime || !str_starts_with($mime, 'image/')) {
                @unlink($target);
                continue;
            }

            if ($ext === 'svg') {
                $this->sanitizeSvg($target);
            }

            $stored[] = $storedName;
            $mapping[$storedBase] = $originalKey;
        }

        $zipArc->close();

        $finalRootAbs = storage_path("app/image-inbox/assets/restaurants/{$restaurantId}/menu/items");

        if (!is_dir($finalRootAbs)) {
            mkdir($finalRootAbs, 0777, true);
        }

        foreach ($stored as $rel) {

            $source = $tmpRoot . '/' . $rel;

            if (!file_exists($source)) {
                continue;
            }

            $target = $finalRootAbs . '/' . $rel;

            if (!rename($source, $target)) {

                if (!copy($source, $target)) {
                    continue;
                }

                unlink($source);
            }
        }

        Storage::disk('local')->makeDirectory('tmp/import-mapping');

        Storage::disk('local')->put(
            "tmp/import-mapping/{$restaurantId}.json",
            json_encode($mapping, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        $this->cleanup($tmpRoot);

        return [
            'path' => $finalRootAbs,
            'files' => $stored,
            'mapping' => $mapping,
        ];
    }

    private function isUnsafePath(string $path): bool
    {
        return str_contains($path, '..')
            || str_contains($path, '\\')
            || str_starts_with($path, '/');
    }

    private function normalizePath(string $path): string
    {
        $path = strtolower($path);
        $parts = explode('/', $path);

        foreach ($parts as &$part) {
            $part = preg_replace('/[^a-z0-9\.\-_]/', '_', $part);
        }

        return implode('/', $parts);
    }

    private function normalizeKey(string $value): string
    {
        $value = Str::ascii(strtolower(trim($value)));
        $value = preg_replace('/[^a-z0-9]+/', '_', $value);

        return trim($value, '_');
    }

    private function cleanup(string $dir): void
    {
        if (!is_dir($dir)) return;

        foreach (scandir($dir) as $file) {
            if ($file === '.' || $file === '..') continue;

            $path = $dir . '/' . $file;

            if (is_dir($path)) {
                $this->cleanup($path);
            } else {
                @unlink($path);
            }
        }

        @rmdir($dir);
    }

    private function sanitizeSvg(string $file): void
    {
        $svg = file_get_contents($file);

        $svg = preg_replace('#<script.*?>.*?</script>#is', '', $svg);
        $svg = preg_replace('#<foreignObject.*?>.*?</foreignObject>#is', '', $svg);
        $svg = preg_replace('#<iframe.*?>.*?</iframe>#is', '', $svg);
        $svg = preg_replace('/on\w+="[^"]*"/i', '', $svg);
        $svg = preg_replace('/xlink:href="https?:\/\/[^"]+"/i', '', $svg);

        file_put_contents($file, $svg);
    }
}
