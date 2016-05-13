<?php
namespace PRaptor\Router\Result;


class JsonResult extends HttpResult
{
    /**
     * @param mixed $data
     * @param int $status
     */
    public function __construct($data, $status = 200)
    {
        $json = json_encode($data);
        parent::__construct($json, $status, 'application/json; charset=UTF-8');
    }
}
