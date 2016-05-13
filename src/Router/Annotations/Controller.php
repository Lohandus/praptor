<?php
namespace PRaptor\Router\Annotations;

use mindplay\annotations\Annotation;

/**
 * @usage('class'=>true, 'inherited'=>true)
 */
class Controller extends Annotation
{
    /**
     * @var string
     */
    public $path = null;
}