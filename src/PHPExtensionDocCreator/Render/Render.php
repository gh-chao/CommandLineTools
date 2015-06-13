<?php


namespace PHPExtensionDocCreator\Render;


class Render
{
    public static function batchRender(array $array, callable $render, $guld)
    {
        $s = [];
        foreach ($array as $item) {
            $s [] = call_user_func($render, $item);
        }

        return implode($guld, $s);
    }

    public static function renderAll(\ReflectionExtension $reflectionExtension)
    {

        $constants = ConstantRender::render($reflectionExtension->getConstants());

        $functions = static::batchRender($reflectionExtension->getFunctions(),
            'PHPExtensionDocCreator\Render\FunctionRender::render', "\n\n");

        $classes   = static::batchRender($reflectionExtension->getClasses(),
            'PHPExtensionDocCreator\Render\ClassRender::render', "\n\n");

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