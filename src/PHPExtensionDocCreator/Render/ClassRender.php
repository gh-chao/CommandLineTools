<?php
namespace PHPExtensionDocCreator\Render;

/**
 * Class ClassRender
 *
 * @package PHPExtensionDocCreator\Render
 * @author  Leo Yang <leoyang@motouch.cn>
 */
class ClassRender
{
    /**
     * @param \ReflectionClass $reflectionClass
     * @return string
     */
    public static function render(\ReflectionClass $reflectionClass)
    {
        $abstract = $reflectionClass->isAbstract() ? ($reflectionClass->isInterface() ? '' : 'abstract ') : '';
        $final    = $reflectionClass->isFinal() ? 'final ' : '';
        $type     = $reflectionClass->isInterface() ? 'interface' : 'class';

        $constans   = static::renderContants($reflectionClass);
        $properties = static::renderProperties($reflectionClass);
        $methods    = static::renderMethods($reflectionClass);

        $extends     = '';
        $parentClass = $reflectionClass->getParentClass();
        if ($parentClass && !$parentClass->isInterface()) {
            $extends = ' extends ' . $parentClass->getName();
        }
        $interface = implode(', ', $reflectionClass->getInterfaceNames());
        if ($interface) {
            $interface = ' implements ' . $interface;
        }

        return <<<EOF
namespace {$reflectionClass->getNamespaceName()}
{
{$abstract}{$final}{$type} {$reflectionClass->getName()}{$extends}{$interface}
{

{$constans}

{$properties}

{$methods}

}
}
EOF;

    }

    /**
     * @param \ReflectionClass $reflectionClass
     * @return string
     */
    public static function renderContants(\ReflectionClass $reflectionClass)
    {
        $array = $reflectionClass->getConstants();
        $s     = [];
        foreach ($array as $name => $value) {
            $s [] = sprintf("const %s = %s;", $name, var_export($value, true));
        }

        return implode("\n", $s);
    }

    /**
     * @param \ReflectionClass $reflectionClass
     * @return string
     */
    public static function renderProperties(\ReflectionClass $reflectionClass)
    {
        $ReflectionProperties = $reflectionClass->getProperties();
        $values               = $reflectionClass->getDefaultProperties();

        $s = [];
        /** @var \ReflectionProperty $reflectionProperty */
        foreach ($ReflectionProperties as $reflectionProperty) {
            if ($reflectionProperty->isPrivate()) {
                continue;
            }
            $value = isset($values[$reflectionProperty->getName()]) ? $values[$reflectionProperty->getName()] : null;
            $s[]   = static::renderProperty($reflectionProperty, $value);
        }

        return implode("\n", $s);
    }

    /**
     * @param \ReflectionProperty $reflectionProperty
     * @param mixed               $value
     * @return string
     */
    public static function renderProperty(\ReflectionProperty $reflectionProperty, $value)
    {
        if ($reflectionProperty->isPublic()) {
            $access = 'public ';
        } else {
            $access = 'protected ';
        }

        $static = $reflectionProperty->isStatic() ? 'static ' : '';
        $value  = var_export($value, true);

        return "{$access}{$static}\${$reflectionProperty->getName()} = {$value};";
    }

    /**
     * @param \ReflectionClass $reflectionClass
     * @return string
     */
    public static function renderMethods(\ReflectionClass $reflectionClass)
    {
        $reflectionMethods = $reflectionClass->getMethods();
        $s                 = [];
        /** @var \ReflectionMethod $reflectionMethod */
        foreach ($reflectionMethods as $reflectionMethod) {
            if ($reflectionMethod->isPrivate()) {
                continue;
            }
            $s[] = MethodRender::render($reflectionMethod);
        }

        return implode("\n\n", $s);
    }

}