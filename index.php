<?php

use PRaptor\Router\Router;
use PRaptor\Router\RouterConfig;

require_once 'vendor/autoload.php';

$controllers = [
    PRaptorDemo\User\UsersController::class
];

$config = new RouterConfig();
$config->baseUrl = 'http://localhost:8080';
$config->cacheDir = __DIR__ . '/runtime';
$config->templateDir = __DIR__ . '/demo';
$config->devMode = true;

$router = new Router($config, $controllers);
$router->dispatch();
