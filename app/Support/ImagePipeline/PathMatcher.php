<?php

namespace App\Support\ImagePipeline;

final class PathMatcher
{
    /** @var array<int,array{match:string,profile:Profile}> */
    private array $rules = [];

    /** @var array<string,Profile> */
    private array $byName = [];

    public function __construct(?array $config = null)
    {
        // 👉 ВАЖНО: передаём ВЕСЬ config
        $config = $config ?? (array) config('image_pipeline');

        // legacy support
        if (isset($config['profiles']) && is_array($config['profiles'])) {
            $rows = [];

            foreach ($config['profiles'] as $name => $row) {
                if (!is_array($row)) continue;

                $row['name'] = $row['name'] ?? (string)$name;
                $rows[] = $row;
            }

            $config = $rows;
        }

        foreach ($config as $row) {
            if (!is_array($row)) continue;

            $profile = new Profile(
                name: (string)($row['name'] ?? 'default'),
                skip: (bool)($row['skip'] ?? false),
                format: $row['format'] ?? null,
                sizes: $row['sizes'] ?? null,
                exact: $row['exact'] ?? null,
                quality: (int)($row['quality'] ?? 80),
                maxKb: isset($row['max_kb']) ? (int)$row['max_kb'] : null,
                hashNames: (bool)($row['hash_names'] ?? false),
                keepSource: (bool)($row['keep_source'] ?? true),
            );

            $match = (string)($row['match'] ?? '');

            if ($match === '') continue;

            $this->rules[] = [
                'match' => $this->normalizePattern($match),
                'profile' => $profile,
            ];

            $this->byName[$profile->name] = $profile;
        }
    }

    public function match(string $relPath): ?Profile
    {
        $relPath = $this->normalizePath($relPath);

        foreach ($this->rules as $rule) {
            if (fnmatch($rule['match'], $relPath)) {
                return $rule['profile'];
            }
        }

        return null;
    }

    public function getByName(string $name): ?Profile
    {
        return $this->byName[$name] ?? null;
    }

    private function normalizePath(string $path): string
    {
        $path = str_replace('\\', '/', $path);
        $path = ltrim($path, '/');

        // убираем assets/ если приходит с public
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
