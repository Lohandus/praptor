<?php

namespace PRaptor\Router\Result;


interface Result
{
    public function respond(ResultContext $context);
}