<?php

if (php_sapi_name() === 'cli-server') {

    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    $file = __DIR__ . '/public' . $path;

    if ($path !== '/' && is_file($file)) {
        $mimeTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'svg' => 'image/svg+xml',
        ];
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        header('Content-Type: ' . ($mimeTypes[$extension] ?? 'application/octet-stream'));
        readfile($file);

        return;
    }
}

require_once __DIR__ . '/public/index.php';
