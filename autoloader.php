<?php
// autoloader

spl_autoload_register(
    function ($class_name) {
        //$file =  __DIR__ . '/lib/' . str_replace('_', '/', $class_name) . '.php';
        if (file_exists(__DIR__ . '/lib/' . str_replace('_', '/', $class_name) . '.php'))
            require_once __DIR__ . '/lib/' . str_replace('_', '/', $class_name) . '.php';
        $file =  __DIR__ .  DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class_name) . '.php';
        //echo '<br>' .  $file .'<br>';
        if (file_exists($file)) {
           //echo '<br>' .  $file .'<br>';
           require_once $file;
        }

    });
