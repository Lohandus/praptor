<?php
namespace PRaptor\Annotations;

use mindplay\annotations\Annotation;

abstract class HttpVerb extends Annotation
{
    /**
     * @var string
     */
    public $path = null;
}