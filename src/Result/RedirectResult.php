<?php
namespace PRaptor\Result;

use PRaptor\Request\RequestContext;

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
        $fullUrl = strpos($this->url, '/') === 0 ? $context->routerOptions->basePath . $this->url : $this->url;
        header("Location: $fullUrl");
    }
}
