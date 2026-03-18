<?php

namespace App\Console\Commands;

use App\Support\ImagePipeline\ImageService;
use App\Support\ImagePipeline\OptimizeOptions;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'images:optimize')]
class ImagesOptimize extends Command
{
    protected $signature = 'images:optimize
       {path?}
       {--dirs=}
       {--profile=}
       {--dry-run}
       {--force}
       {--clean-names}
       {--hash-names}
       {--retina}
       {--purge-webp}
       {--delete-sources}
       {--max-source-mb=40}
       {--only=}
       {--report-json=}
       {--report-csv=}
   ';

    protected $description = 'Optimize images';

    public function handle(ImageService $service): int
    {
        $assetsBase = rtrim(config('image_pipeline.paths.assets'), DIRECTORY_SEPARATOR);

        $roots = [];

        if ($this->option('dirs')) {
            $roots = explode(',', $this->option('dirs'));
        } else {
            $roots = [$this->argument('path') ?? ''];
        }

        $opt = new OptimizeOptions(
            dryRun: (bool)$this->option('dry-run'),
            force: (bool)$this->option('force'),
            cleanNames: (bool)$this->option('clean-names'),
            hashNames: (bool)$this->option('hash-names'),
            retina: (bool)$this->option('retina'),
            purgeWebp: (bool)$this->option('purge-webp'),
            deleteSources: (bool)$this->option('delete-sources'),
            maxSourceMb: (int)$this->option('max-source-mb'),
            forcedProfile: $this->option('profile'),
            reportJsonPath: $this->option('report-json'),
            reportCsvPath: $this->option('report-csv'),
        );

        foreach ($roots as $rootRel) {

            $rootRel = trim($rootRel, '/');

            $abs = $rootRel
                ? $assetsBase . '/' . $rootRel
                : $assetsBase;

            if (!is_dir($abs)) {
                $this->warn("Skip: {$rootRel}");
                continue;
            }

            $report = $service->optimize($abs, $opt);

            $this->info("Optimized: {$report->optimized}");
            $this->line("Skipped: {$report->skipped}");
        }

        return self::SUCCESS;
    }
}
