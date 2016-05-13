<?php
namespace PRaptor\Router;


class RouterConfig
{
    /**
     * @var string
     */
    public $baseUrl;

    /**
     * @var string
     */
    public $cacheDir;

    /**
     * @var string
     */
    public $templateDir;
    
    /**
     * @var boolean When true, all caches are disabled.
     */
    public $devMode = false;
}