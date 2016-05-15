<?php
namespace PRaptor\Router\Interceptor;


use PRaptor\Router\RequestContext;
use PRaptor\Router\Result\Result;

interface Interceptor
{
    /**
     * @param RequestContext $requestContext
     * @param InterceptorStack $stack
     * @return Result
     */
    public function intercept(RequestContext $requestContext, InterceptorStack $stack);
}