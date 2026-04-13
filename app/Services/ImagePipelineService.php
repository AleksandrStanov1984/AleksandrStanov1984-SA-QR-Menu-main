<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class ImagePipelineService
{
    /**
     * @throws \Exception
     */
    public function uploadAndProcess(
        UploadedFile $file,
        int $restaurantId,
        string $segment = 'menu/items'
    ): string
    {
        $filename = (string) Str::uuid();
        $extension = strtolower($file->extension() ?: 'jpg');

        $inboxPath = "image-inbox/assets/restaurants/{$restaurantId}/{$segment}";

        $targetDir = storage_path('app/' . $inboxPath);

        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $targetPath = $targetDir . '/' . $filename . '.' . $extension;

        $realPath = $file->getRealPath();

        if (!$realPath || !file_exists($realPath)) {
            throw new \Exception('Temp file missing BEFORE save');
        }

        if (!copy($realPath, $targetPath)) {
            throw new \Exception('Failed to copy uploaded file');
        }

        if (!file_exists($targetPath)) {
            throw new \Exception('File not found after copy');
        }

        Artisan::call('images:cron', [
            '--retina' => true,
            '--delete-sources' => true,
        ]);

        return "restaurants/{$restaurantId}/{$segment}/{$filename}.webp";
    }

    /**
     * @throws \Exception
     */
    public function replace(
        UploadedFile $file,
        int $restaurantId,
        ?string $oldPath,
        string $segment = 'menu/items'
    ): string
    {
        if ($oldPath) {
            app(ImageService::class)->delete($oldPath);
        }

        return $this->uploadAndProcess($file, $restaurantId, $segment);
    }

    /**
     * @throws \Exception
     */
    public function uploadSvg(UploadedFile $file, string $targetPath): string
    {
        $filename = (string) Str::uuid() . '.svg';

        $publicDir = public_path("assets/{$targetPath}");

        if (!file_exists($publicDir)) {
            mkdir($publicDir, 0777, true);
        }

        $realPath = $file->getRealPath();

        if (!$realPath || !file_exists($realPath)) {
            throw new \Exception('Temp SVG missing');
        }

        $content = file_get_contents($realPath);

        if (!$content) {
            throw new \Exception('Invalid SVG');
        }

        // sanitize
        $lc = strtolower($content);

        if (
            str_contains($lc, '<script') ||
            str_contains($lc, 'onload=') ||
            str_contains($lc, 'onerror=') ||
            str_contains($lc, 'javascript:')
        ) {
            throw new \Exception('Unsafe SVG');
        }

        $publicFile = $publicDir . '/' . $filename;

        if (!file_put_contents($publicFile, $content)) {
            throw new \Exception('Failed to save SVG');
        }

        return "{$targetPath}/{$filename}";
    }
}
