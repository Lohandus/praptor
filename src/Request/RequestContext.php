<?php
namespace PRaptor\Request;

use PRaptor\RouterOptions;
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