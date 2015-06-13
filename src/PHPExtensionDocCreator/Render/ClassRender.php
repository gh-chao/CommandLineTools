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
        $abstract = $reflectionClass->isAbstract() ? ($reflectionClass->isInterface() ? '' :'abstract ') : '';

        $final    = $reflectionClass->isFinal() ? 'final ' : '';
        $type     = $reflectionClass->isInterface() ? 'interface' : 'class';

        $constans  = static::renderContants($reflectionClass->getConstants());
        $propertys = static::renderProperties($reflectionClass->getProperties());
        $methods   = static::renderMethods($reflectionClass->getMethods());

        $interface = implode(', ', $reflectionClass->getInterfaceNames());
        if ($interface) {
            $interface = ' implements '. $interface;
        }

        return <<<EOF
namespace {$reflectionClass->getNamespaceName()}
{
{$abstract}{$final}{$type} {$reflectionClass->getName()}{$interface}
{

{$constans}

{$propertys}

{$methods}

}
}
EOF;

    }

    /**
     * @param array $array
     * @return string
     */
    public static function renderContants(array $array)
    {
        $s = [];
        foreach ($array as $name => $value) {
            $s [] = sprintf("const %s = %s;", $name, var_export($value, true));
        }

        return implode("\n", $s);
    }

    /**
     * @param array $array
     * @return string
     */
    public static function renderProperties(array $array)
    {
        $s = [];
        foreach ($array as $reflectionProperty) {
            $s[] = static::renderProperty($reflectionProperty);
        }

        return implode("\n", $s);
    }

    /**
     * @param \ReflectionProperty $reflectionProperty
     * @return string
     */
    public static function renderProperty(\ReflectionProperty $reflectionProperty)
    {
        $private   = $reflectionProperty->isPrivate() ? 'private' : '';
        $public    = $reflectionProperty->isPublic() ? 'public' : '';
        $protected = $reflectionProperty->isProtected() ? 'protected' : '';
        $static    = $reflectionProperty->isStatic() ? 'static ' : '';

        $reflectionProperty->setAccessible(true);

        $obj = new \stdClass();

        $default = $reflectionProperty->isDefault() ? ' = ' . var_export($reflectionProperty->getValue($obj), true) : '';

        return "{$private}{$public}{$protected} {$static}\${$reflectionProperty->getName()}{$default};";
    }

    /**
     * @param array $array
     * @return string
     */
    public static function renderMethods(array $array)
    {
        $s = [];
        foreach ($array as $reflectionMethod) {
            $s[] = MethodRender::render($reflectionMethod);
        }

        return implode("\n\n", $s);
    }

}