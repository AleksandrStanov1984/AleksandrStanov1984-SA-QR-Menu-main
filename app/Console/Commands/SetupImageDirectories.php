<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SetupImageDirectories extends Command
{
    protected $signature = 'images:setup';

    protected $description = 'Create image pipeline directories';

    public function handle(): int
    {
        $directories = [

            // =========================
            // STORAGE (INBOX)
            // =========================
            storage_path('app/image-inbox/assets/restaurants'),
            storage_path('app/image-inbox/assets/system'),
            storage_path('app/image-inbox/assets/system/banners'),
            storage_path('app/image-inbox/assets/system/icons'),
            storage_path('app/image-inbox/assets/system/fallback'),
            storage_path('app/image-inbox/assets/system/author'),
            storage_path('app/image-inbox/assets/system/og'),
            storage_path('app/image-inbox/assets/system/qr'),
            storage_path('app/reports'),

            // =========================
            // PUBLIC (RUNTIME)
            // =========================
            public_path('assets/restaurants'),
            public_path('assets/system'),
            public_path('assets/system/banners'),
            public_path('assets/system/icons'),
            public_path('assets/system/fallback'),
            public_path('assets/system/author'),
            public_path('assets/system/og'),
            public_path('assets/system/qr'),
        ];

        foreach ($directories as $dir) {
            try {
                File::ensureDirectoryExists($dir, 0755, true);
                $this->line("OK: {$dir}");
            } catch (\Throwable $e) {
                $this->error("FAIL: {$dir}");
            }
        }

        $this->info('Image directories ready.');

        return Command::SUCCESS;
    }
}
