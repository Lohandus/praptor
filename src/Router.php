<?php
namespace PRaptor;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use mindplay\annotations\AnnotationCache;
use mindplay\annotations\Annotations;
use PRaptor\Annotations\RouterAnnotations;
use PRaptor\Interceptor\Interceptor;
use PRaptor\Request\RequestContext;
use PRaptor\Request\RequestProcessor;
use PRaptor\Result\Result;
use PRaptor\Result\Results;
use ReflectionMethod;

class Router
{
    /**
     * @var RouterOptions
     */
    private $options;

    /**
     * @var string[]
     */
    private $controllers = [];

    /**
     * @var Interceptor[]
     */
    private $interceptors = [];

    /**
     * @param RouterOptions $options
     */
    public function __construct(RouterOptions $options)
    {
        $this->options = $options;
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
        Annotations::$config['cache'] = new AnnotationCache($this->options->cacheDir);
        RouterAnnotations::register();
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
            'cacheFile' => $this->options->cacheDir . '/route.cache',
            'cacheDisabled' => $this->options->devMode
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
        
        $request->routerOptions = $this->options;
        $request->requestUri = $uri;

        return $request;
    }
}