<?php

spl_autoload_register(function (string $class): void {
    $base = dirname(dirname(__DIR__)) . '/src/';
    $relative = str_replace('App\\', '', $class);
    $file = $base . str_replace('\\', '/', $relative) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});
