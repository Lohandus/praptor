<?php
namespace PRaptor\Router;

use ReflectionMethod;

class RequestContext
{
    /**
     * @var RouterConfig
     */
    public $config;

    /**
     * @var ReflectionMethod
     */
    public $controllerMethod;

    /**
     * @var string
     */
    public $requestUri;
}