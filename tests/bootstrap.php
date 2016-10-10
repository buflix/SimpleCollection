<?php

namespace Tests\SimpleCollection;

error_reporting(E_ALL | E_STRICT);

spl_autoload_register(function ($sClassName) {
    if (0 === strpos($sClassName, 'Tests\SimpleCollection\\')) {
        $sClassName = str_replace('Tests\\', '', $sClassName);
        $sPath      = __DIR__ . DIRECTORY_SEPARATOR . strtr($sClassName, '\\', '/') . '.php';
        if (is_file($sPath) && is_readable($sPath)) {
            require_once $sPath;

            return true;
        }
    }
});

require_once __DIR__ . "/../vendor/autoload.php";