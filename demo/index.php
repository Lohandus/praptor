<?php

use PRaptor\Router\Router;
use PRaptor\Router\RouterOptions;
use PRaptorDemo\Exception\ErrorHandler;
use PRaptorDemo\Security\AuthorizationInterceptor;
use PRaptorDemo\Security\LogInterceptor;
use PRaptorDemo\Security\PublicAccess;

require_once __DIR__ . '/../vendor/autoload.php';

session_start();

$options = new RouterOptions();
$options->baseUrl     = 'http://localhost:8080';
$options->cacheDir    = __DIR__ . '/runtime';
$options->templateDir = __DIR__ . '/';
$options->devMode     = true;

$router = new Router($options);

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
