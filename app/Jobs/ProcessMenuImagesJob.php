<?php

namespace App\Jobs;

use App\Models\Item;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Queueable;

class ProcessMenuImagesJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public int $timeout = 1200;
    public int $tries = 1;

    public function __construct(
        public int $restaurantId
    ) {}

    public function handle(): void
    {
        $statusKey = "import:status:{$this->restaurantId}";
        $resultKey = "import:result:{$this->restaurantId}";

        Cache::put($statusKey, 'processing', now()->addMinutes(30));
        Cache::forget($resultKey);

        try {
            Log::info('JOB STARTED', [
                'restaurant_id' => $this->restaurantId,
            ]);

            // =========================
            // INBOX
            // =========================
            $inbox = "image-inbox/assets/restaurants/{$this->restaurantId}/menu/items";
            $inboxAbs = storage_path('app/' . $inbox);

            if (!is_dir($inboxAbs)) {
                Log::error('INBOX NOT FOUND', ['path' => $inboxAbs]);
                Cache::forget($statusKey);
                return;
            }

            // =========================
            // MAPPING
            // =========================
            $mappingPath = "tmp/import-mapping/{$this->restaurantId}.json";
            $mapping = [];

            if (Storage::disk('local')->exists($mappingPath)) {
                $mapping = json_decode(
                    Storage::disk('local')->get($mappingPath),
                    true
                ) ?: [];
            }

            Log::info('MAPPING LOADED', ['count' => count($mapping)]);

            // =========================
            // ITEMS
            // =========================
            $items = Item::query()
                ->whereHas('section', fn($q) =>
                $q->where('restaurant_id', $this->restaurantId)
                )
                ->get()
                ->keyBy('key');

            // =========================
            // FILTER INBOX (STRICT)
            // =========================
            $validFiles = [];
            $ignored = [];

            foreach (glob($inboxAbs . '/*') as $file) {

                if (!is_file($file)) continue;

                $filename = pathinfo($file, PATHINFO_FILENAME);

                $key = $mapping[$filename] ?? $filename;

                if (!isset($items[$key])) {

                    $ignored[] = $filename;
                    @unlink($file);

                    continue;
                }

                $validFiles[] = $file;
            }

            Log::info('INBOX FILTER RESULT', [
                'valid' => count($validFiles),
                'ignored' => count($ignored),
                'ignored_files' => $ignored,
            ]);

            if (empty($validFiles)) {
                Log::warning('NO VALID FILES TO PROCESS');
                Cache::put($statusKey, 'done', now()->addMinutes(30));
                return;
            }

            // =========================
            // COLLECT OLD PATHS
            // =========================
            $oldPaths = [];

            foreach ($validFiles as $file) {

                $filename = pathinfo($file, PATHINFO_FILENAME);
                $key = $mapping[$filename] ?? $filename;

                if (!isset($items[$key])) continue;

                $item = $items[$key];

                if (!empty($item->image_path)) {
                    $oldPaths[] = $item->image_path;
                }
            }

            $oldPaths = array_unique($oldPaths);

            // =========================
            // DELETE OLD FILES
            // =========================
            $deleted = 0;

            foreach ($oldPaths as $path) {

                $full = public_path('assets/' . ltrim($path, '/'));

                $dir = dirname($full);
                $name = pathinfo($full, PATHINFO_FILENAME);

                $base = preg_replace('/(@2x|\-\d+)$/', '', $name);

                foreach (glob($dir . '/' . $base . '*') ?: [] as $file) {
                    if (is_file($file)) {
                        @unlink($file);
                        $deleted++;
                    }
                }
            }

            Log::info('OLD FILES DELETED', [
                'count' => $deleted,
            ]);

            // =========================
            // PIPELINE
            // =========================
            Artisan::call('images:cron', [
                '--delete-sources' => true,
            ]);

            // =========================
            // PUBLIC FILES
            // =========================
            $publicDir = public_path("assets/restaurants/{$this->restaurantId}/menu/items");

            if (!is_dir($publicDir)) {
                Cache::put($statusKey, 'error', now()->addMinutes(30));
                return;
            }

            $files = glob($publicDir . '/*.webp');

            // =========================
            // MATCH
            // =========================
            $updates = [];
            $unmatched = [];

            foreach ($files as $file) {

                $filename = pathinfo($file, PATHINFO_FILENAME);
                $pipelineName = preg_replace('/(-\d+|@2x)$/', '', $filename);

                $key = $mapping[$pipelineName] ?? $pipelineName;

                if (!$key || !isset($items[$key])) {
                    $unmatched[] = $filename;
                    continue;
                }

                $updates[] = [
                    'id' => $items[$key]->id,
                    'path' => "restaurants/{$this->restaurantId}/menu/items/{$filename}.webp"
                ];
            }

            // =========================
            // DB UPDATE
            // =========================
            foreach ($updates as $u) {
                Item::where('id', $u['id'])->update([
                    'image_path' => $u['path']
                ]);
            }

            Log::info('DB UPDATED', [
                'updated' => count($updates),
                'unmatched' => count($unmatched),
            ]);

            // =========================
            // RESULT
            // =========================
            Cache::put($resultKey, [
                'processed' => count($updates),
                'unmatched' => count($unmatched),
            ], now()->addMinutes(30));

            Cache::put($statusKey, 'done', now()->addMinutes(30));

        } catch (\Throwable $e) {

            Log::error('IMPORT ERROR', [
                'message' => $e->getMessage(),
            ]);

            Cache::put($statusKey, 'error', now()->addMinutes(30));
        }
    }
}
