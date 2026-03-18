<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImagePipelineTest extends Command
{
    protected $signature = 'images:test {restaurant=1}';

    protected $description = 'Test full image pipeline (inbox → cron → optimize)';

    public function handle(): int
    {
        $restaurantId = (int) $this->argument('restaurant');

        $this->info("Running image pipeline test for restaurant: {$restaurantId}");

        // 1. Пути
        $inbox = storage_path("app/image-inbox/assets/restaurants/{$restaurantId}/menu/items");
        $public = public_path("assets/restaurants/{$restaurantId}/menu/items");

        File::ensureDirectoryExists($inbox);
        File::ensureDirectoryExists($public);

        // 2. Копируем тестовую картинку
        $testSource = base_path('tests/fixtures/test.jpg');

        if (!file_exists($testSource)) {
            $this->error("Test image not found: tests/fixtures/test.jpg");
            return self::FAILURE;
        }

        $target = $inbox . '/test.jpg';

        File::copy($testSource, $target);

        $this->info("Copied test image → inbox");

        // 3. Запуск cron
        $this->call('images:cron', [
            '--retina' => true,
            '--force' => true,
        ]);

        // 4. Проверка результата
        $webp = $public . '/test.webp';
        $webp2x = $public . '/test@2x.webp';

        $this->line('');

        if (file_exists($webp)) {
            $this->info("✔ WebP created");
        } else {
            $this->error("✖ WebP missing");
        }

        if (file_exists($webp2x)) {
            $this->info("✔ Retina created");
        } else {
            $this->warn("⚠ Retina missing");
        }

        return self::SUCCESS;
    }
}
