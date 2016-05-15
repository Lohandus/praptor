<?php
namespace PRaptor\Router\Interceptor;

use PRaptor\Router\RequestContext;
use PRaptor\Router\Result\Result;

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
     * @var array
     */
    private $injections = array();

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
            return $callback($this->injections);
        }
        
        $interceptor = $this->interceptors[$this->current++];
        return $interceptor->intercept($this->requestContext, $this);
    }

    /**
     * @param string $varName
     * @param mixed $value
     */
    public function inject($varName, $value)
    {
        $this->injections[$varName] = $value;
    }

    /**
     * @param string $varName
     * @return mixed
     */
    public function getInjectedValue($varName)
    {
        return $this->injections[$varName];
    }
}