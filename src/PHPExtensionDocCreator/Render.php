<?php


namespace PHPExtensionDocCreator;


use PHPExtensionDocCreator\Render\ClassRender;
use PHPExtensionDocCreator\Render\ConstantRender;
use PHPExtensionDocCreator\Render\FunctionRender;

class Render
{
    public static function renderClasses(\ReflectionExtension $reflectionExtension)
    {
        $s = [];
        foreach ($reflectionExtension->getClasses() as $reflectionClass) {
            $s[] = ClassRender::render($reflectionClass);
        }
        return implode("\n\n", $s);
    }

    public static function renderFunctions(\ReflectionExtension $reflectionExtension)
    {
        $s = [];
        foreach ($reflectionExtension->getFunctions() as $reflectionFunction) {
            $s[] = FunctionRender::render($reflectionFunction);
        }
        return implode("\n\n", $s);
    }

    public static function renderAll(\ReflectionExtension $reflectionExtension)
    {
        $constants = ConstantRender::render($reflectionExtension->getConstants());
        $functions = static::renderFunctions($reflectionExtension);
        $classes   = static::renderClasses($reflectionExtension);

        return <<<EOF
<?php

/**
 * Extension Name : {$reflectionExtension->getName()}
 * Extension Version : {$reflectionExtension->getVersion()}
 */
namespace
{
{$constants}
}

{$functions}

{$classes}
EOF;
    }
}