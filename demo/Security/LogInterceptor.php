<?php
namespace PRaptorDemo\Security;

use PRaptor\Interceptor\Interceptor;
use PRaptor\Interceptor\InterceptorStack;
use PRaptor\Request\RequestContext;
use PRaptor\Result\Result;

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