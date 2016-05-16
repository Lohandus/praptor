<?php
namespace PRaptorDemo\Exception;

use Exception;
use PRaptor\Router\Interceptor\Interceptor;
use PRaptor\Router\Interceptor\InterceptorStack;
use PRaptor\Router\RequestContext;
use PRaptor\Router\Result\Result;
use PRaptor\Router\Result\Results;
use TreklinkDemo\Exception\BusinessException;

class ErrorHandler implements Interceptor
{
    /**
     * @param RequestContext $requestContext
     * @param InterceptorStack $stack
     * @return Result
     */
    public function intercept(RequestContext $requestContext, InterceptorStack $stack)
    {
        try {
            return $stack->next();
        } catch (Exception $e) {
            error_log("Internal error: $e");
            return Results::http('Internal error', 500);
        }
    }
}