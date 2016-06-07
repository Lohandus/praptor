<?php
namespace PRaptorDemo\Security;

use mindplay\annotations\Annotations;
use PRaptor\Interceptor\Interceptor;
use PRaptor\Interceptor\InterceptorStack;
use PRaptor\Request\RequestContext;
use PRaptor\Result\Result;
use PRaptor\Result\Results;

class AuthorizationInterceptor implements Interceptor
{
    /**
     * @param RequestContext $requestContext
     * @param InterceptorStack $stack
     * @return Result
     */
    public function intercept(RequestContext $requestContext, InterceptorStack $stack)
    {
        $annotations = Annotations::ofClass($requestContext->controllerMethod->getDeclaringClass(), PublicAccess::class);
        $isPublic = count($annotations) > 0;

        if ($isPublic)
            return $stack->next();

        if (key_exists('loggedInUser', $_SESSION)) {
            $requestContext->inject('loggedInUser', $_SESSION['loggedInUser']);
            return $stack->next();
        }

        return Results::http('Unauthorized', 401);

    }
}
