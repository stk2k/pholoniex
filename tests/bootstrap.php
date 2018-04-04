<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

spl_autoload_register(function($class) {
    if (strpos($class, 'Pholoniex\\') === 0) {
        $dir = strcasecmp(substr($class, -4), 'Test') ? 'src/' : 'tests/';
        $name = substr($class, strlen('Pholoniex'));
        require __DIR__ . '/../' . $dir . strtr($name, '\\', DIRECTORY_SEPARATOR) . '.php';
    }
});
