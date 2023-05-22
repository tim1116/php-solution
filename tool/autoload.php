<?php
/**
 * File: autoload.php
 * PROJECT_NAME: php-solution
 */


return function () {
    spl_autoload_register(function ($class) {
        include_once __DIR__ . "/src/" . $class . '.php';
    });
};