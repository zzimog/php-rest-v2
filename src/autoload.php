<?php

ini_set("html_errors", 0);

set_error_handler(function ($code, $msg) {
    throw new Exception($msg, $code);
}, E_WARNING);

spl_autoload_register(function ($class) {
    $file = str_replace("\\", DIRECTORY_SEPARATOR, $class) . ".php";
    $filepath = __DIR__ . DIRECTORY_SEPARATOR . $file;

    if (file_exists($filepath)) {
        require_once $filepath;
    }
});
