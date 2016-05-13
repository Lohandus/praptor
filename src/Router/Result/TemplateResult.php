<?php
namespace PRaptor\Router\Result;

use Twig_Environment;
use Twig_Loader_Filesystem;

class TemplateResult implements Result
{
    /**
     * @var string
     */
    private $templateFile;
    
    /**
     * @var array
     */
    private $templateContext;
    
    /**
     * @var int
     */
    private $status;

    /**
     * TemplateResult constructor.
     * @param string $templateFile
     * @param array $templateContext
     * @param int $status
     */
    public function __construct($templateFile, $templateContext, $status = 200)
    {
        $this->templateFile = $templateFile;
        $this->templateContext = $templateContext;
        $this->status = $status;
    }

    public function respond(ResultContext $context)
    {
        $twig = $this->getTwig($context);
        $body = $twig->render($this->templateFile, $this->templateContext);
        
        $httpResult = new HttpResult($body, $this->status);
        $httpResult->respond($context);
    }

    /**
     * @param ResultContext $context
     * @return Twig_Environment
     */
    private function getTwig(ResultContext $context)
    {
        $loader = new Twig_Loader_Filesystem($context->config->templateDir);

        return new Twig_Environment($loader, array(
            'cache' => $context->config->cacheDir . '/twig',
            'auto_reload' => $context->config->devMode
        ));
    }
}
