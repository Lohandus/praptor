<?php
namespace PRaptor\Router\Result;


class Results
{
    /**
     * @param string $url
     * @return RedirectResult
     */
    public static function redirect($url)
    {
        return new RedirectResult($url);
    }
    
    /**
     * @param mixed $data
     * @param int $status
     * @return JsonResult
     */
    public static function json($data, $status = null)
    {
        return new JsonResult($data, $status);
    }
    
    public static function nothing()
    {
        return new NothingResult();
    }

    /**
     * @param string $body
     * @param int $status
     * @param string $contentType
     * @return HttpResult
     */
    public static function http($body = "", $status = 200, $contentType = 'text/html; charset=UTF-8')
    {
        return new HttpResult($body, $status, $contentType);
    }

    /**
     * @param string $templateFile
     * @param array $templateContext
     * @param int $status
     * @return TemplateResult
     */
    public static function template($templateFile, $templateContext = array(), $status = 200)
    {
        return new TemplateResult($templateFile, $templateContext, $status);
    }
}
