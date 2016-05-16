<?php

use PRaptor\Router\Router;
use PRaptor\Router\RouterConfig;
use PRaptorDemo\Exception\ErrorHandler;
use PRaptorDemo\Security\AuthorizationInterceptor;
use PRaptorDemo\Security\LogInterceptor;
use PRaptorDemo\Security\PublicAccess;

require_once 'vendor/autoload.php';

session_start();

$config = new RouterConfig();
$config->baseUrl     = 'http://localhost:8080';
$config->cacheDir    = __DIR__ . '/runtime';
$config->templateDir = __DIR__ . '/demo';
$config->devMode     = true;

$router = new Router($config);

$router->registerCustomAnnotations([
    'public-access' => PublicAccess::class
]);

$router->setInterceptors([
    new LogInterceptor(),
    new ErrorHandler(),
    new AuthorizationInterceptor()
]);

$router->setControllerClasses([
    PRaptorDemo\User\UsersController::class
]);

$router->dispatch();
