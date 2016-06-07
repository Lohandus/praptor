<?php
namespace PRaptor\Result;

use PRaptor\Request\RequestContext;
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
    public function __construct($templateFile, $templateContext = array(), $status = 200)
    {
        $this->templateFile = $templateFile;
        $this->templateContext = $templateContext;
        $this->status = $status;
    }

    public function respond(RequestContext $context)
    {
        $twig = $this->getTwig($context);
        $body = $twig->render($this->templateFile, $this->templateContext);
        
        $httpResult = new HttpResult($body, $this->status);
        $httpResult->respond($context);
    }

    /**
     * @param RequestContext $context
     * @return Twig_Environment
     */
    private function getTwig(RequestContext $context)
    {
        $loader = new Twig_Loader_Filesystem($context->routerOptions->templateDir);

        $twig = new Twig_Environment($loader, array(
            'cache' => $context->routerOptions->cacheDir . '/twig',
            'auto_reload' => $context->routerOptions->devMode
        ));
        
        if ($context->routerOptions->twigExtensions !== null)
            $twig->setExtensions($context->routerOptions->twigExtensions);
        
        return $twig;
    }
}
