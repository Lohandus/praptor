<?php
/**
 * Created by PhpStorm.
 * User: lohandus
 * Date: 12/05/16
 * Time: 17:26
 */

namespace PRaptor\Router\Annotations;


use mindplay\annotations\Annotation;

abstract class HttpVerb extends Annotation
{
    /**
     * @var string
     */
    public $path = null;
}