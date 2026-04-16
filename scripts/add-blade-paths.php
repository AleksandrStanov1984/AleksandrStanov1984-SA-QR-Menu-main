<?php

$resourcesBase = realpath(__DIR__ . '/../resources');

if (!$resourcesBase) {
    echo "❌ Cannot find resources directory\n";
    exit(1);
}

$targets = [
    [
        'dir' => $resourcesBase . DIRECTORY_SEPARATOR . 'views',
        'match' => fn(SplFileInfo $file) => str_ends_with($file->getFilename(), '.blade.php'),
        'buildPath' => function (string $path) use ($resourcesBase) {
            $relative = str_replace($resourcesBase . DIRECTORY_SEPARATOR, '', $path);
            return 'resources/' . str_replace('\\', '/', $relative);
        },
        'comment' => fn(string $fullPath) => "{{-- {$fullPath} --}}\n",
    ],
    [
        'dir' => $resourcesBase . DIRECTORY_SEPARATOR . 'js',
        'match' => fn(SplFileInfo $file) => $file->getExtension() === 'js',
        'buildPath' => function (string $path) use ($resourcesBase) {
            $relative = str_replace($resourcesBase . DIRECTORY_SEPARATOR, '', $path);
            return 'resources/' . str_replace('\\', '/', $relative);
        },
        'comment' => fn(string $fullPath) => "// {$fullPath}\n",
    ],
    [
        'dir' => $resourcesBase . DIRECTORY_SEPARATOR . 'css',
        'match' => fn(SplFileInfo $file) => $file->getExtension() === 'css',
        'buildPath' => function (string $path) use ($resourcesBase) {
            $relative = str_replace($resourcesBase . DIRECTORY_SEPARATOR, '', $path);
            return 'resources/' . str_replace('\\', '/', $relative);
        },
        'comment' => fn(string $fullPath) => "/* {$fullPath} */\n",
    ],
];

foreach ($targets as $target) {

    if (!is_dir($target['dir'])) {
        echo "⚠ skip missing dir: {$target['dir']}\n";
        continue;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($target['dir'], RecursiveDirectoryIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {

        if (!$file->isFile()) continue;
        if (!($target['match'])($file)) continue;

        $path = $file->getRealPath();
        if (!$path) continue;

        $content = file_get_contents($path);
        if ($content === false) {
            echo "❌ read error {$path}\n";
            continue;
        }

        $fullPath = ($target['buildPath'])($path);

        // 🔥 КЛЮЧЕВОЙ FIX — проверяем первые 300 символов
        $head = substr($content, 0, 300);

        if (str_contains($head, $fullPath)) {
            echo "⏭ skip {$fullPath}\n";
            continue;
        }

        $comment = ($target['comment'])($fullPath);

        $result = file_put_contents($path, $comment . $content);

        if ($result === false) {
            echo "❌ write error {$fullPath}\n";
        } else {
            echo "✔ added {$fullPath}\n";
        }
    }
}

echo "\nDONE\n";
