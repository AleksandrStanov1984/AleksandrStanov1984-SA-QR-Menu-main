<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'images:cron')]
class ImagesCron extends Command
{
    protected $signature = 'images:cron
        {--dry-run}
        {--force}
        {--retina}
        {--clean-names}
        {--hash-names}
        {--purge-webp}
        {--delete-sources}
        {--max-source-mb=}
    ';

    protected $description = 'Inbox → public/assets → optimize';

    public function handle(): int
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $cfg = config('image_pipeline');

        $inboxAbs = storage_path('app/' . $cfg['inbox_dir']);
        $assetsInboxAbs = $inboxAbs . '/assets';

        $manifestAbs = storage_path('app/' . $cfg['manifest_path']);

        $reportJson = storage_path('app/' . $cfg['reports_dir'] . '/images.json');
        $reportCsv  = storage_path('app/' . $cfg['reports_dir'] . '/images.csv');

        $dryRun = (bool) $this->option('dry-run');

        if (!is_dir($assetsInboxAbs)) {
            $this->warn('Inbox empty');
            return self::SUCCESS;
        }

        $files = File::allFiles($assetsInboxAbs);

        $changedDirs = [];
        $moved = 0;

        foreach ($files as $file) {

            $abs = $file->getPathname();

            $rel = $this->relFrom($assetsInboxAbs, $abs);
            $rel = ltrim(str_replace('\\', '/', $rel), '/');

            // 🔥 FIX: защита от двойного assets/
            if (str_starts_with($rel, 'assets/')) {
                $rel = substr($rel, strlen('assets/') );
            }

            $dst = public_path('assets/' . $rel);

            $dir = trim(dirname($rel), '/');
            if ($dir && $dir !== '.') {
                $changedDirs[$dir] = true;
            }

            if ($dryRun) {
                $this->line("[dry-run] {$rel}");
                continue;
            }

            File::ensureDirectoryExists(dirname($dst));
            File::copy($abs, $dst);
            @unlink($abs);

            $moved++;
        }

        if ($moved === 0) {
            $this->info('Nothing to move');
            return self::SUCCESS;
        }

        $dirs = array_keys($changedDirs);

        $args = [
            '--dirs' => implode(',', $dirs),
            '--report-json' => $reportJson,
            '--report-csv' => $reportCsv,
        ];

        if ($this->option('retina') ?? true) $args['--retina'] = true;
        if ($this->option('clean-names')) $args['--clean-names'] = true;
        if ($this->option('hash-names')) $args['--hash-names'] = true;
        if ($this->option('purge-webp')) $args['--purge-webp'] = true;
        if ($this->option('delete-sources')) $args['--delete-sources'] = true;
        if ($this->option('force')) $args['--force'] = true;
        if ($this->option('max-source-mb')) {
            $args['--max-source-mb'] = (int)$this->option('max-source-mb');
        }

        return $this->call('images:optimize', $args);
    }

    private function relFrom(string $base, string $abs): string
    {
        $base = str_replace('\\','/', $base);
        $abs = str_replace('\\','/', $abs);

        return ltrim(str_replace($base . '/', '', $abs), '/');
    }
}
