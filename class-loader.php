<?php

function classLoader($class) {

    $file =  str_replace('\\',DIRECTORY_SEPARATOR, __DIR__ . '/classes/'.$class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
}

spl_autoload_register('classLoader');
