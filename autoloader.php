<?php

spl_autoload_register(
    function ($class_name) {
        $file = __DIR__ . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR  . str_replace('_', DIRECTORY_SEPARATOR, $class_name) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }

        $file = __DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class_name) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    });
