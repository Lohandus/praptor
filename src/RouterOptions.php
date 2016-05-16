<?php
namespace PRaptor;

class RouterOptions
{
    /**
     * @var string If you site is available at 'http://localhost/my-site/', basePath should be 'my-site'.
     */
    public $basePath = '';

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