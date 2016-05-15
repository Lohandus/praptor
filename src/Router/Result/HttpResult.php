<?php
namespace PRaptor\Router\Result;


use PRaptor\Router\RequestContext;

class HttpResult implements Result
{
    /**
     * @var string
     */
    private $body;
    
    /**
     * @var int
     */
    private $status;
    
    /**
     * @var string
     */
    private $contentType;
    
    /**
     * @var array
     */
    private $headers = array();

    /**
     * @param string $body
     * @param int $status
     * @param string $contentType
     */
    public function __construct($body = "", $status = 200, $contentType = 'text/html; charset=UTF-8')
    {
        $this->body = $body;
        $this->status = $status;
        $this->contentType = $contentType;
    }

    /**
     * @param string $header
     * @return $this
     */
    public function header($header)
    {
        $this->headers[] = $header;
        return $this;
    }

    public function respond(RequestContext $context)
    {
        http_response_code($this->status);
        header('Content-Type: ' . $this->contentType);

        foreach ($this->headers as $header)
            header($header);

        echo $this->body;
    }
}
