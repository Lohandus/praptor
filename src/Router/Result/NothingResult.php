<?php
namespace PRaptor\Router\Result;


use PRaptor\Router\RequestContext;

class NothingResult implements Result
{
    public function respond(RequestContext $context)
    {
    }
}
