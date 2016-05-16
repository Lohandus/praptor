<?php
namespace PRaptor\Result;

use PRaptor\Request\RequestContext;

interface Result
{
    public function respond(RequestContext $context);
}