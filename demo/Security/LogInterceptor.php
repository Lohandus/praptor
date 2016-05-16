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
        if ($requestContext->routerOptions->devMode)
            error_log("### Request $requestContext->requestUri > $requestContext->controllerMethodFullName");

        return $stack->next();
    }
}