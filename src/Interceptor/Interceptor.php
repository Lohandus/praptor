<?php
namespace PRaptor\Interceptor;

use PRaptor\Request\RequestContext;
use PRaptor\Result\Result;

interface Interceptor
{
    /**
     * @param RequestContext $requestContext
     * @param InterceptorStack $stack
     * @return Result
     */
    public function intercept(RequestContext $requestContext, InterceptorStack $stack);
}