<?php

use mindplay\annotations\Annotations;
use PRaptor\Router\Router;
use PRaptor\Router\RouterConfig;
use PRaptorDemo\Security\AuthorizationInterceptor;
use PRaptorDemo\Security\LogInterceptor;
use PRaptorDemo\Security\PublicAccess;

require_once 'vendor/autoload.php';

session_start();

Annotations::getManager()->registry['public-access'] = PublicAccess::class;

$controllers = [
    PRaptorDemo\User\UsersController::class
];

$config = new RouterConfig();
$config->baseUrl = 'http://localhost:8080';
$config->cacheDir = __DIR__ . '/runtime';
$config->templateDir = __DIR__ . '/demo';
$config->devMode = true;

$router = new Router($config, $controllers);

$router->addInterceptor(new LogInterceptor());
$router->addInterceptor(new AuthorizationInterceptor());
$router->dispatch();
