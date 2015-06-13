<?php
namespace PHPExtensionDocCreator\Render;


class MethodRender extends FunctionRender
{

    /**
     * @param \ReflectionMethod $reflectionMethod
     * @return string
     */
    public static function render($reflectionMethod)
    {
        $annotation = static::renderAnnotation($reflectionMethod);
        $abstract   = $reflectionMethod->isAbstract() ? ($reflectionMethod->getDeclaringClass()->isInterface() ? '' : 'abstract ') : '';
        $private    = $reflectionMethod->isPrivate() ? 'private' : '';
        $public     = $reflectionMethod->isPublic() ? 'public' : '';
        $protected  = $reflectionMethod->isProtected() ? 'protected' : '';
        $static     = $reflectionMethod->isStatic() ? 'static ' : '';

        $reflectionMethod->setAccessible(true);

        $fun = sprintf("%s%s%s%s %sfunction %s (%s) %s",
            $abstract,
            $private,
            $public,
            $protected,
            $static,
            $reflectionMethod->getName(),
            static::renderParameters($reflectionMethod->getParameters()),
            $reflectionMethod->isAbstract() ? ';' : '{}'
        );

        return $annotation . $fun;
    }

}