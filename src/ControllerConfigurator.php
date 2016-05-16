<?php
namespace PRaptor;

use FastRoute\RouteCollector;
use mindplay\annotations\Annotations;
use PRaptor\Annotations\Controller;
use PRaptor\Annotations\Delete;
use PRaptor\Annotations\Get;
use PRaptor\Annotations\HttpVerb;
use PRaptor\Annotations\Post;
use ReflectionClass;

class ControllerConfigurator
{
    /**
     * @var RouteCollector
     */
    private $routeCollector;

    private $httpVerbsAnnotations = [Get::class, Post::class, Delete::class];
    private $httpVerbsByAnnotations = [
        Get::class    => 'GET',
        Post::class   => 'POST',
        Delete::class => 'DELETE'
    ];

    /**
     * @param RouteCollector $routeCollector
     */
    public function __construct(RouteCollector $routeCollector)
    {
        $this->routeCollector = $routeCollector;
    }

    /**
     * @param string $controller
     * @throws RouterConfigurationException
     */
    public function configure($controller)
    {
        $controllerAnnotations = Annotations::ofClass($controller, Controller::class);

        if (!count($controllerAnnotations))
            throw new RouterConfigurationException("Controller class $controller should be annotated with @controller");

        $controllerPath = $controllerAnnotations[0]->path;
        if ($controllerPath === null)
            throw new RouterConfigurationException("@controller annotation of class $controller should have path set. E.g. @controller('path' => '/user')");

        $reflector = new ReflectionClass($controller);
        $methods = $reflector->getMethods();

        foreach ($methods as $method) {
            $this->configureControllerMethod($method, $controllerPath);
        }
    }

    /**
     * @param \ReflectionMethod $method
     * @param string $controllerPath
     * @throws RouterConfigurationException
     */
    private function configureControllerMethod(\ReflectionMethod $method, $controllerPath)
    {
        /** @var HttpVerb $httpVerbAnnotation */
        $httpVerbAnnotation = null;
        $annotations = Annotations::ofMethod($method);

        foreach ($annotations as $annotation) {
            if (in_array(get_class($annotation), $this->httpVerbsAnnotations)) {
                $httpVerbAnnotation = $annotation;
                break;
            }
        }

        if ($httpVerbAnnotation === null)
            return;

        $methodPath = $httpVerbAnnotation->path;
        $fullPath = $controllerPath . $methodPath;
        $methodFullName = $method->getDeclaringClass()->getName() . '::' . $method->getName();
        $httpVerb = $this->httpVerbsByAnnotations[get_class($httpVerbAnnotation)];

        $this->routeCollector->addRoute($httpVerb, $fullPath, $methodFullName);
    }
}