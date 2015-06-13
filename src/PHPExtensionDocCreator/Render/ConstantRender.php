<?php


namespace PHPExtensionDocCreator\Render;


class ConstantRender
{

    public static function render($constants)
    {
        $s = '';
        foreach ($constants as $key => $value) {
            $s .= sprintf("define('%s', %s);\n", $key, var_export($value, true));
        }

        return $s;
    }

}