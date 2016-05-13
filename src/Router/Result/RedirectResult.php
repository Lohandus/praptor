<?php
namespace PRaptor\Router\Result;


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

    public function respond(ResultContext $context)
    {
        $fullUrl = strpos($this->url, '/') === 0 ? $context->config->baseUrl . $this->url : $this->url;
        header("Location: $fullUrl");
    }
}
