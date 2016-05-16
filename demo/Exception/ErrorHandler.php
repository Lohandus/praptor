<?php
namespace PRaptorDemo\Exception;

use Exception;
use PRaptor\Interceptor\Interceptor;
use PRaptor\Interceptor\InterceptorStack;
use PRaptor\Request\RequestContext;
use PRaptor\Result\Result;
use PRaptor\Result\Results;

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