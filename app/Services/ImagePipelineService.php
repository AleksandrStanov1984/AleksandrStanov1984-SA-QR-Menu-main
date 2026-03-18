<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class ImagePipelineService
{
    public function uploadAndProcess(UploadedFile $file, int $restaurantId): string
    {
        $filename = (string) Str::uuid();
        $extension = strtolower($file->extension() ?: 'jpg');

        $inboxPath = "image-inbox/assets/restaurants/{$restaurantId}/menu/items";

        // 🔥 полный путь
        $targetDir = storage_path('app/' . $inboxPath);

        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // 🔥 путь назначения
        $targetPath = $targetDir . '/' . $filename . '.' . $extension;

        // 🔥 берём реальный временный файл
        $realPath = $file->getRealPath();

        if (!$realPath || !file_exists($realPath)) {
            throw new \Exception('Temp file missing BEFORE save');
        }

        // 🔥 КОПИРУЕМ вручную (самый надёжный способ)
        if (!copy($realPath, $targetPath)) {
            throw new \Exception('Failed to copy uploaded file');
        }

        if (!file_exists($targetPath)) {
            throw new \Exception('File not found after copy');
        }

        // 🔥 запускаем pipeline
        Artisan::call('images:cron', [
            '--retina' => true,
            '--delete-sources' => true,
        ]);

        return "restaurants/{$restaurantId}/menu/items/{$filename}.webp";
    }

    public function replace(UploadedFile $file, int $restaurantId, ?string $oldPath): string
    {
        if ($oldPath) {
            app(\App\Services\ImageService::class)->delete($oldPath);
        }

        return $this->uploadAndProcess($file, $restaurantId);
    }

    public function uploadSvg(UploadedFile $file, string $targetPath): string
    {
        $filename = (string) \Illuminate\Support\Str::uuid() . '.svg';

        // inbox
        $inboxDir = storage_path("app/image-inbox/assets/{$targetPath}");
        if (!file_exists($inboxDir)) {
            mkdir($inboxDir, 0777, true);
        }

        $inboxFile = $inboxDir . '/' . $filename;

        $realPath = $file->getRealPath();

        if (!$realPath || !file_exists($realPath)) {
            throw new \Exception('Temp SVG missing');
        }

        if (!copy($realPath, $inboxFile)) {
            throw new \Exception('Failed to copy SVG to inbox');
        }

        // public
        $publicDir = public_path("assets/{$targetPath}");
        if (!file_exists($publicDir)) {
            mkdir($publicDir, 0777, true);
        }

        $publicFile = $publicDir . '/' . $filename;

        if (!copy($inboxFile, $publicFile)) {
            throw new \Exception('Failed to move SVG to public');
        }

        return "{$targetPath}/{$filename}";
    }
}
