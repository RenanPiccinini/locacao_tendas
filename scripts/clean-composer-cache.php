<?php

$paths = [
    '/home/u639274314/.composer/cache',
    __DIR__ . '/../.composer-cache',
];

foreach ($paths as $path) {
    if (!is_dir($path)) {
        continue;
    }

    try {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $item) {
            if ($item->isDir()) {
                @rmdir($item->getRealPath());
            } else {
                @unlink($item->getRealPath());
            }
        }

        @rmdir($path);
    } catch (\Throwable $e) {
        // Silently continue
    }
}

exit(0);
