<?php

namespace PRaptor\Router\Result;


use PRaptor\Router\RequestContext;

interface Result
{
    public function respond(RequestContext $context);
}