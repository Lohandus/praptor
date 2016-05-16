<?php
namespace PRaptor\Router;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use mindplay\annotations\AnnotationCache;
use mindplay\annotations\Annotations;
use PRaptor\Router\Interceptor\Interceptor;
use PRaptor\Router\Result\Result;
use PRaptor\Router\Result\Results;
use ReflectionMethod;

class Router
{
    /**
     * @var RouterConfig
     */
    private $config;

    /**
     * @var string[]
     */
    private $controllers = [];

    /**
     * @var Interceptor[]
     */
    private $interceptors = [];

    /**
     * @param RouterConfig $config
     */
    public function __construct(RouterConfig $config)
    {
        $this->config = $config;
        $this->configureAnnotations();
    }

    /**
     * @param string[] $controllers
     */
    public function setControllerClasses(array $controllers)
    {
        $this->controllers = $controllers;
    }

    /**
     * @param Interceptor[] $interceptors
     */
    public function setInterceptors(array $interceptors)
    {
        $this->interceptors = $interceptors;
    }

    private function configureAnnotations()
    {
        Annotations::$config['cache'] = new AnnotationCache($this->config->cacheDir);
        \PRaptor\Router\Annotations\Package::register();
    }

    /**
     * @param array $annotationClassesByName
     */
    public function registerCustomAnnotations($annotationClassesByName)
    {
        $manager = Annotations::getManager();
        foreach (array_keys($annotationClassesByName) as $name)
            $manager->registry[$name] = $annotationClassesByName[$name];
    }
    
    /**
     * @return Dispatcher
     */
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

        return \FastRoute\cachedDispatcher($routeDefinitionCallback, $options);
    }

    public function dispatch()
    {
        $uri = rawurldecode(strtok($_SERVER['REQUEST_URI'], '?'));
        $httpMethod = $_SERVER['REQUEST_METHOD'];

        $dispatcher = $this->configureRouterDispatcher();
        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);

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
                $requestProcessor = new RequestProcessor($this->interceptors);
                $result = $requestProcessor->process($requestContext, $vars);
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
        $request = new RequestContext();
        
        if ($handler !== null) {
            $parts = explode('::', $handler);
            $class = $parts[0];
            $method = $parts[1];
            
            $request->controllerMethod = new ReflectionMethod($class, $method);
            $request->controllerMethodFullName = $handler;
        }
        
        $request->config = $this->config;
        $request->requestUri = $uri;

        return $request;
    }
}

class RouterConfigurationException extends \Exception {}
