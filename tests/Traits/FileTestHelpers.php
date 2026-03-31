<?php

namespace Tests\Traits;

trait FileTestHelpers
{
    protected function fakePublicImage(string $path): string
    {
        $fullPath = public_path("assets/" . $path);

        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0777, true);
        }

        file_put_contents($fullPath, 'fake-image');

        return $path;
    }
}
