<?php
namespace PRaptor\Router;

use PRaptor\Router\Interceptor\Interceptor;
use PRaptor\Router\Interceptor\InterceptorStack;
use PRaptor\Router\Result\Result;
use PRaptor\Router\Result\Results;
use ReflectionMethod;
use ReflectionParameter;

class RequestProcessor
{
    /**
     * @var Interceptor[]
     */
    private $interceptors;

    /**
     * RequestProcessor constructor.
     * @param Interceptor[] $interceptors
     */
    public function __construct(array &$interceptors)
    {
        $this->interceptors = $interceptors;
    }

    /**
     * @param RequestContext $requestContext
     * @param array $pathParams
     * @return Result
     * @throws RouterConfigurationException
     */
    public function process(RequestContext $requestContext, array &$pathParams)
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

        throw new RouterConfigurationException("Controller method $requestContext->controllerMethodFullName should return an instance of Router\\Result");
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