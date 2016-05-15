<?php
namespace PRaptorDemo\Security;

use PRaptor\Router\Interceptor\Interceptor;
use PRaptor\Router\Interceptor\InterceptorStack;
use PRaptor\Router\RequestContext;
use PRaptor\Router\Result\Result;

class LogInterceptor implements Interceptor
{
    /**
     * @param RequestContext $requestContext
     * @param InterceptorStack $stack
     * @return Result
     */
    public function intercept(RequestContext $requestContext, InterceptorStack $stack)
    {
        error_log("### Request " . $requestContext->requestUri . " -> " . $requestContext->controllerMethod->getDeclaringClass()->getName() . '::' . $requestContext->controllerMethod->getName());
        $result = $stack->next();
        
        error_log('--- Result: ' . get_class($result));
        
        return $result;
    }
}