<?php
namespace PRaptor\Interceptor;

use PRaptor\Request\RequestContext;
use PRaptor\Result\Result;

class InterceptorStack
{
    /**
     * @var Interceptor[]
     */
    private $interceptors;

    /**
     * @var callable
     */
    private $callback;
    
    /**
     * @var RequestContext
     */
    private $requestContext;

    private $current = 0;

    /**
     * InterceptorStack constructor.
     * @param Interceptor[] $interceptors
     * @param RequestContext $requestContext
     * @param callable $callback
     */
    public function __construct(&$interceptors, $requestContext, $callback)
    {
        $this->interceptors = $interceptors;
        $this->callback = $callback;
        $this->requestContext = $requestContext;
    }

    /**
     * @return Result
     */
    public function next()
    {
        if ($this->current >= count($this->interceptors)) {
            $callback = $this->callback;
            return $callback();
        }
        
        $interceptor = $this->interceptors[$this->current++];
        return $interceptor->intercept($this->requestContext, $this);
    }
}