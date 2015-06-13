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
        $type = '';
        if ($reflectionParameter->isArray()) {
            $type = 'array ';
        } elseif ($reflectionParameter->isCallable()) {
            $type = 'callable ';
        } elseif ($reflectionParameter->getClass()) {
            $type = $reflectionParameter->getClass()->getName() . ' ';
        }

        $default = '';
        if ($reflectionParameter->isOptional()) {
            $default = " = NULL";
            try {
                if ($reflectionParameter->isDefaultValueAvailable()) {
                    $default = sprintf(" = %s", var_export($reflectionParameter->getDefaultValue(), true));
                } elseif ($reflectionParameter->isDefaultValueConstant()) {
                    $default = sprintf(" = %s", var_export($reflectionParameter->getDefaultValueConstantName(), true));
                }
            } catch (\ReflectionException $e) {
            }
        }

        $reference = $reflectionParameter->isPassedByReference() ? '&' : '';

        if ($reflectionParameter->getName() == '...') {
            return "{$type}{$reference}\$_ = \"...\"";
        }

        return "{$type}{$reference}\${$reflectionParameter->getName()}{$default}";
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
        $s                    = "/**\n";

        /** @var \ReflectionParameter $reflectionParameter */
        foreach ($reflectionParameters as $reflectionParameter) {
            if ($reflectionParameter->getName() == '...') {
                continue;
            }
            $s .= sprintf(
                " * @param %s$%s\n",
                $reflectionParameter->getClass() ? $reflectionParameter->getClass()->getName() . ' ' : '',
                $reflectionParameter->getName()
            );
        }

        if ($reflectionMethod->getName() == '__toString') {
            $s .= " *\n * @return string\n */\n";
        } else {
            $s .= " *\n * @return mixed\n */\n";
        }

        return $s;
    }

}