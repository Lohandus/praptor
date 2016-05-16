<?php

use PRaptor\Router;
use PRaptor\RouterOptions;
use PRaptorDemo\Exception\ErrorHandler;
use PRaptorDemo\Security\AuthorizationInterceptor;
use PRaptorDemo\Security\LogInterceptor;
use PRaptorDemo\Security\PublicAccess;

require_once __DIR__ . '/../vendor/autoload.php';

session_start();

$options = new RouterOptions();
$options->basePath    = '';
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
