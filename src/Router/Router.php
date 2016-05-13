<?php
namespace PRaptor\Router;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use mindplay\annotations\AnnotationCache;
use mindplay\annotations\Annotations;
use PRaptor\Router\Result\Result;
use PRaptor\Router\Result\ResultContext;
use PRaptor\Router\Result\Results;

class Router
{
    /**
     * @var RouterConfig
     */
    private $config;

    /**
     * @var array
     */
    private $controllers;

    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * Router constructor.
     * @param RouterConfig $config
     * @param array $controllers
     */
    public function __construct($config, $controllers)
    {
        $this->config = $config;
        $this->controllers = $controllers;

        $this->configureAnnotations();
        $this->configureRouterDispatcher();
    }

    private function configureAnnotations()
    {
        Annotations::$config['cache'] = new AnnotationCache($this->config->cacheDir);
        \PRaptor\Router\Annotations\Package::register();
    }

    private function configureRouterDispatcher()
    {
        $routeDefinitionCallback = function (RouteCollector $routeCollector) {
            $configurator = new ControllerConfigurator($routeCollector);
            
            foreach ($this->controllers as $controller)
                $configurator->configure($controller);
        };

        $options = [
            'cacheFile' => $this->config->cacheDir . '/route.cache',
            'cacheDisabled' => $this->config->devMode
        ];

        $this->dispatcher = \FastRoute\cachedDispatcher($routeDefinitionCallback, $options);
    }

    public function dispatch()
    {
        $uri = rawurldecode(strtok($_SERVER['REQUEST_URI'], '?'));
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $routeInfo = $this->dispatcher->dispatch($httpMethod, $uri);

        /** @var Result $result */
        $result = null;

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                $result = Results::http("Not Found", 404);
                break;

            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                $result = Results::http("Not Allowed", 405)->header("Allow: " . implode(", ", $allowedMethods));
                break;

            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                $result = $this->processRequest($handler, $vars);
                break;
        }
        
        $this->processResult($result);
    }

    private function processRequest($handler, $pathParams)
    {
        $parts = explode('::', $handler);
        $class = $parts[0];
        $method = $parts[1];

        $reflectionMethod = new \ReflectionMethod($class, $method);
        $methodArgs = [];

        foreach ($reflectionMethod->getParameters() as $reflectionParam) {
            $methodArgs[] = $this->getParamValue($reflectionParam, $pathParams);
        }

        $controller = new $class();
        $result = $reflectionMethod->invokeArgs($controller, $methodArgs);

        if ($result === null)
            return Results::nothing();

        if ($result instanceof Result)
            return $result;

        throw new RouterConfigurationException("Controller method $handler should return an instance of Router\\Result");
    }

    /**
     * @param Result $result
     */
    private function processResult($result)
    {
        $context = new ResultContext();
        $context->config = $this->config;

        $result->respond($context);
    }

    /**
     * @param \ReflectionParameter $reflectionParam
     * @param array $pathParams
     * @return mixed
     */
    private function getParamValue($reflectionParam, $pathParams)
    {
        $paramName = $reflectionParam->getName();

        if (array_key_exists($paramName, $pathParams))
            return $pathParams[$paramName];

        if (array_key_exists($paramName, $_REQUEST))
            return $_REQUEST[$paramName];

        if ($reflectionParam->isDefaultValueAvailable())
            return $reflectionParam->getDefaultValue();

        return null;
    }
}

class RouterConfigurationException extends \Exception {}
