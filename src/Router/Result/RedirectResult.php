<?php
namespace PRaptor\Router\Result;


use PRaptor\Router\RequestContext;

class RedirectResult implements Result
{
    /**
     * @var string
     */
    private $url;

    /**
     * @param string $url
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    public function respond(RequestContext $context)
    {
        $fullUrl = strpos($this->url, '/') === 0 ? $context->routerOptions->baseUrl . $this->url : $this->url;
        header("Location: $fullUrl");
    }
}
