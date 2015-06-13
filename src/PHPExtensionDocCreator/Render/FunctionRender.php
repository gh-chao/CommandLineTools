<?php
namespace PHPExtensionDocCreator\Render;

/**
 * Class FunctionRender
 *
 * @package PHPExtensionDocCreator\Render
 * @author  Leo Yang <leoyang@motouch.cn>
 */
class FunctionRender
{

    /**
     * @param array $reflectionParameters
     * @return string
     */
    public static function renderParameters(array $reflectionParameters)
    {
        $s = array();
        /** @var \ReflectionParameter $reflectionParameter */
        foreach ($reflectionParameters as $reflectionParameter) {
            if ($reflectionParameter->getName() == '...') {
                continue;
            }
            $s[] = static::renderParameter($reflectionParameter);
        }

        return implode(', ', $s);
    }

    /**
     * @param \ReflectionParameter $reflectionParameter
     * @return string
     */
    public static function renderParameter(\ReflectionParameter $reflectionParameter)
    {
        $isArray     = $reflectionParameter->isArray() ? 'array ' : '';
        $isCallable  = $reflectionParameter->isCallable() ? 'callable' : '';
        $isClass     = $reflectionParameter->getClass() ? get_class($reflectionParameter->getClass()) . ' ' : '';
        $isReference = $reflectionParameter->isPassedByReference() ? '&' : '';

        $default = '';
        try {
            if ($reflectionParameter->isDefaultValueConstant()) {
                $default = sprintf(" = %s", var_export($reflectionParameter->getDefaultValueConstantName(), true));
            }
        } catch (\ReflectionException $e) {
        }
        try {
            if ($reflectionParameter->isDefaultValueAvailable()) {
                $default = sprintf(" = %s", var_export($reflectionParameter->getDefaultValue(), true));
            }
        } catch (\ReflectionException $e) {
        }

        return "{$isArray}{$isCallable}{$isClass}{$isReference}\${$reflectionParameter->getName()}{$default}";
    }

    /**
     * @param \ReflectionFunction $reflectionMethod
     * @return string
     */
    public static function render($reflectionMethod)
    {
        $annotation = static::renderAnnotation($reflectionMethod);
        $fun        = sprintf("namespace {$reflectionMethod->getNamespaceName()}{ function %s (%s) {} }",
            $reflectionMethod->getName(),
            static::renderParameters($reflectionMethod->getParameters())
        );

        return $annotation . $fun;
    }

    /**
     * @param \ReflectionFunction|\ReflectionMethod $reflectionMethod
     * @return string
     */
    public static function renderAnnotation($reflectionMethod)
    {
        $reflectionParameters = $reflectionMethod->getParameters();
        $s = "/**\n";

        /** @var \ReflectionParameter $reflectionParameter */
        foreach ($reflectionParameters as $reflectionParameter) {
            if ($reflectionParameter->getName() == '...') {
                continue;
            }
            $s .= sprintf(
                " * @param %s$%s\n",
                $reflectionParameter->getClass() ? get_class($reflectionParameter->getClass()) . ' ' : '',
                $reflectionParameter->getName()
            );
        }

        if ($reflectionMethod->getName()=='__toString') {
            $s .= " *\n * @return string\n */\n";
        } else {
            $s .= " *\n * @return mixed\n */\n";
        }

        return $s;
    }

}