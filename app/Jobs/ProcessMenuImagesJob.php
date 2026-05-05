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

            $inbox = "image-inbox/assets/restaurants/{$this->restaurantId}/menu/items";

            if (!Storage::disk('local')->exists($inbox)) {
                Cache::forget($statusKey);
                return;
            }

            $mappingPath = "tmp/import-mapping/{$this->restaurantId}.json";

            $mapping = [];

            if (Storage::disk('local')->exists($mappingPath)) {
                $mapping = json_decode(
                    Storage::disk('local')->get($mappingPath),
                    true
                ) ?: [];
            }

            Storage::disk('local')->delete(
                "tmp/import-unmatched/{$this->restaurantId}.json"
            );

            Artisan::call('images:cron', [
                '--retina' => true,
                '--delete-sources' => true,
            ]);

            $publicDir = public_path("assets/restaurants/{$this->restaurantId}/menu/items");

            if (!is_dir($publicDir)) {
                Cache::put($statusKey, 'error', now()->addMinutes(30));
                return;
            }

            $files = array_filter(
                glob($publicDir . '/*.webp'),
                fn($file) => filemtime($file) >= now()->subMinutes(10)->timestamp
            );

            if (!$files) {
                Cache::put($statusKey, 'done', now()->addMinutes(30));
                return;
            }

            $items = Item::query()
                ->whereHas('section', fn($q) =>
                $q->where('restaurant_id', $this->restaurantId)
                )
                ->get()
                ->keyBy('key');

            $unmatched = [];
            $updates = [];

            foreach ($files as $file) {

                $filename = pathinfo($file, PATHINFO_FILENAME);

                $pipelineName = preg_replace('/(-\d+|@2x)$/', '', $filename);

                $key = $mapping[$pipelineName] ?? null;

                if (!$key || !isset($items[$key])) {
                    $unmatched[] = $filename;
                    continue;
                }

                $updates[] = [
                    'id' => $items[$key]->id,
                    'path' => "restaurants/{$this->restaurantId}/menu/items/{$filename}.webp"
                ];
            }

            foreach ($updates as $u) {
                Item::where('id', $u['id'])->update([
                    'image_path' => $u['path']
                ]);
            }

            if (!empty($unmatched)) {
                Storage::disk('local')->put(
                    "tmp/import-unmatched/{$this->restaurantId}.json",
                    json_encode($unmatched, JSON_PRETTY_PRINT)
                );
            }

            Cache::put($resultKey, [
                'processed' => count($updates),
                'unmatched' => count($unmatched),
            ], now()->addMinutes(30));

            Cache::put($statusKey, 'done', now()->addMinutes(30));

        } catch (\Throwable $e) {

            Cache::put($statusKey, 'error', now()->addMinutes(30));
        }
    }
}
