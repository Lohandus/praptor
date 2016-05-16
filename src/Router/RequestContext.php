<?php
namespace PRaptor\Router;

use ReflectionMethod;

class RequestContext
{
    /**
     * @var RouterOptions
     */
    public $routerOptions;

    /**
     * @var ReflectionMethod
     */
    public $controllerMethod;

    /**
     * @var string
     */
    public $controllerMethodFullName;

    /**
     * @var string
     */
    public $requestUri;
}