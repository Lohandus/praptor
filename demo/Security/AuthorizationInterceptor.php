<?php
namespace PRaptorDemo\Security;

use mindplay\annotations\Annotations;
use PRaptor\Router\Interceptor\InterceptorStack;
use PRaptor\Router\RequestContext;
use PRaptor\Router\Result\Result;
use PRaptor\Router\Result\Results;


class AuthorizationInterceptor implements \PRaptor\Router\Interceptor\Interceptor
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
            $stack->inject('loggedInUser', $_SESSION['loggedInUser']);
            return $stack->next();
        }

        return Results::http('Unauthorized', 401);

    }
}
