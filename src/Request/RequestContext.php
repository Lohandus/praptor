<?php
namespace PRaptor\Request;

use PRaptor\AcceptHeader;
use PRaptor\RouterOptions;
use ReflectionMethod;

class RequestContext
{
    /**
     * @var RouterOptions
     */
    public $routerOptions;

    /**
     * @var ReflectionMethod
     */
    public $controllerMethod;

    /**
     * @var string
     */
    public $controllerMethodFullName;

    /**
     * @var string
     */
    public $requestUri;

    /**
     * @var array
     */
    private $injections = [];

    /**
     * @param string $mediaType E.g. text/html
     * @return bool
     */
    public function accepts($mediaType)
    {
        $acceptHeader = new AcceptHeader($_SERVER['HTTP_ACCEPT']);
        foreach ($acceptHeader as $item) {
            if ($item['raw'] == $mediaType)
                return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isAjax()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
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
    
    public function isInjected($varName)
    {
        return array_key_exists($varName, $this->injections);
    }
}