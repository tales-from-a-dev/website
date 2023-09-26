<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

if (true === (bool) $_SERVER['APP_DEBUG']) {
    umask(0000);
}

if (false === (bool) $_SERVER['APP_DEBUG']) {
    // ensure fresh cache
    (new Symfony\Component\Filesystem\Filesystem())->remove(__DIR__.'/../var/cache/test');
}
