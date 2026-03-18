<?php

namespace App\Support\ImagePipeline;

final class ProfileResolver
{
    /** @var array<string, Profile> */
    private array $profiles = [];

    /** @var array<string, mixed> */
    private array $raw;

    public function __construct(array $profiles, ?array $rawConfig = null)
    {
        $this->profiles = $profiles;

        // 👉 ВАЖНО: берём ВЕСЬ config
        $config = $rawConfig ?? (array) config('image_pipeline');

        // поддержка legacy
        if (isset($config['profiles']) && is_array($config['profiles'])) {
            $this->raw = $config['profiles'];
        } else {
            $this->raw = $config;
        }
    }

    public function resolve(string $publicRelPath): ?Profile
    {
        $publicRelPath = $this->normalizePath($publicRelPath);

        foreach ($this->raw as $name => $cfg) {

            if (!is_array($cfg)) continue;

            $patterns = $cfg['match'] ?? [];

            // нормализация (строка → массив)
            if (is_string($patterns)) {
                $patterns = [$patterns];
            }

            foreach ($patterns as $pattern) {

                $pattern = $this->normalizePattern($pattern);

                if (fnmatch($pattern, $publicRelPath)) {
                    return $this->profiles[(string)$name] ?? null;
                }
            }
        }

        return null;
    }

    private function normalizePath(string $path): string
    {
        $path = str_replace('\\', '/', $path);
        $path = ltrim($path, '/');

        if (str_starts_with($path, 'assets/')) {
            $path = substr($path, strlen('assets/'));
        }

        return $path;
    }

    private function normalizePattern(string $pattern): string
    {
        $pattern = str_replace('\\', '/', $pattern);
        $pattern = ltrim($pattern, '/');

        return $pattern;
    }
}
