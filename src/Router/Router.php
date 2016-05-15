<?php
namespace PRaptor\Router;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use mindplay\annotations\AnnotationCache;
use mindplay\annotations\Annotations;
use PRaptor\Router\Interceptor\Interceptor;
use PRaptor\Router\Interceptor\InterceptorStack;
use PRaptor\Router\Result\Result;
use PRaptor\Router\Result\Results;
use ReflectionMethod;
use ReflectionParameter;

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
     * @var Interceptor[]
     */
    private $interceptors;

    /**
     * Router constructor.
     * @param RouterConfig $config
     * @param string[] $controllers
     */
    public function __construct(RouterConfig $config, array $controllers)
    {
        $this->config = $config;
        $this->controllers = $controllers;

        $this->configureAnnotations();
        $this->configureRouterDispatcher();
    }

    /**
     * @param Interceptor $interceptor
     */
    public function addInterceptor(Interceptor $interceptor)
    {
        $this->interceptors[] = $interceptor;
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
        $requestContext = null;

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                $requestContext = $this->buildRequestContext($uri);
                $result = Results::http("Not Found", 404);
                break;

            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                $requestContext = $this->buildRequestContext($uri);
                $result = Results::http("Not Allowed", 405)->header("Allow: " . implode(", ", $allowedMethods));
                break;

            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                $requestContext = $this->buildRequestContext($uri, $handler);
                $result = $this->processRequest($handler, $vars, $requestContext);
                break;
        }

        $result->respond($requestContext);
    }

    /**
     * @param string $uri
     * @param string $handler
     * @return RequestContext
     */
    private function buildRequestContext($uri, $handler = null)
    {
        $reflectionMethod = null;
        if ($handler !== null) {
            $parts = explode('::', $handler);
            $class = $parts[0];
            $method = $parts[1];
            $reflectionMethod = new ReflectionMethod($class, $method);
        }

        $request = new RequestContext();
        $request->config = $this->config;
        $request->controllerMethod = $reflectionMethod;
        $request->requestUri = $uri;

        return $request;
    }

    /**
     * @param string $handler
     * @param array $pathParams
     * @param RequestContext $requestContext
     * @return Result
     * @throws RouterConfigurationException
     */
    private function processRequest($handler, array &$pathParams, RequestContext $requestContext)
    {
        $reflectionMethod = $requestContext->controllerMethod;

        $invokeControllerMethod = function(&$injections) use ($reflectionMethod, &$pathParams) {
            $controller = $this->instantiateController($reflectionMethod, $injections);
            $methodArgs = $this->getMethodArgs($reflectionMethod, $pathParams);
            return $reflectionMethod->invokeArgs($controller, $methodArgs);
        };

        $interceptorStack = new InterceptorStack($this->interceptors, $requestContext, $invokeControllerMethod);
        $result = $interceptorStack->next();

        if ($result === null)
            return Results::nothing();

        if ($result instanceof Result)
            return $result;

        throw new RouterConfigurationException("Controller method $handler should return an instance of Router\\Result");
    }
    
    /**
     * @param ReflectionMethod $reflectionMethod
     * @param array $injections
     * @return mixed
     */
    private function instantiateController(ReflectionMethod $reflectionMethod, array &$injections)
    {
        $reflectionClass = $reflectionMethod->getDeclaringClass();
        $reflectionConstructor = $reflectionClass->getConstructor();
        $constructorArgs = array();

        if ($reflectionConstructor !== null) {
            foreach ($reflectionConstructor->getParameters() as $reflectionParam)
                $constructorArgs[] = $this->getConstructorParamValue($reflectionParam, $injections);
        }

        return $reflectionClass->newInstanceArgs($constructorArgs);
    }

    /**
     * @param ReflectionParameter $reflectionParam
     * @param array $injections
     * @return mixed
     */
    private function getConstructorParamValue(ReflectionParameter $reflectionParam, array &$injections)
    {
        $paramName = $reflectionParam->getName();
        
        if (array_key_exists($paramName, $injections))
            return $injections[$paramName];

        if ($reflectionParam->isDefaultValueAvailable())
            return $reflectionParam->getDefaultValue();

        return null;
    }
    
    /**
     * @param ReflectionMethod $reflectionMethod
     * @param array $pathParams
     * @return array
     */
    private function getMethodArgs(ReflectionMethod $reflectionMethod, array $pathParams)
    {
        $methodArgs = [];

        foreach ($reflectionMethod->getParameters() as $reflectionParam) {
            $methodArgs[] = $this->getParamValue($reflectionParam, $pathParams);
        }
        return $methodArgs;
    }

    /**
     * @param ReflectionParameter $reflectionParam
     * @param array $pathParams
     * @return mixed
     */
    private function getParamValue(ReflectionParameter $reflectionParam, array $pathParams)
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
