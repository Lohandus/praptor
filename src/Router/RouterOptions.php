<?php
namespace PRaptor\Router;


class RouterOptions
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