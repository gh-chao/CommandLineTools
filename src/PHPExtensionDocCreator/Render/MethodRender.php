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

        $final    = $reflectionMethod->isFinal() ? 'final ' : '';
        $abstract = $reflectionMethod->isAbstract() ? ($reflectionMethod->getDeclaringClass()->isInterface() ? '' : 'abstract ') : '';

        if ($reflectionMethod->isPublic()) {
            $access = 'public ';
        } else {
            $access = 'protected ';
        }

        $static = $reflectionMethod->isStatic() ? 'static ' : '';
        $params = static::renderParameters($reflectionMethod->getParameters());

        return $annotation . "{$final}{$abstract}{$access}{$static}function {$reflectionMethod->getName()} ({$params})" .
        ($reflectionMethod->isAbstract() ? ';' : '{}');
    }

}