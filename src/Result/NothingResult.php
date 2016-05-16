<?php
namespace PRaptor\Result;

use PRaptor\Request\RequestContext;

class NothingResult implements Result
{
    public function respond(RequestContext $context)
    {
    }
}
