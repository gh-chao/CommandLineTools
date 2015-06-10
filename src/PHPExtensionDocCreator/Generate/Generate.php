<?php
namespace PHPExtensionDocCreator\Generate;

/**
 * Extension Name => yac
 * Extension Version => 1.0
 */
class Generate
{

    public function output($extension_name, $output_dir)
    {

        $reflectionExtension = new \ReflectionExtension($extension_name);
        echo $this->render($reflectionExtension);
    }


    protected function render(\ReflectionExtension $reflectionExtension)
    {

        $constants = $this->renderConstants($reflectionExtension->getConstants());
        $functions = $this->renderFunctions($reflectionExtension->getFunctions());
        $classes   = $this->renderClasses($reflectionExtension->getClasses());

        return <<<EOF
<?php

/**
 * Extension Name : {$reflectionExtension->getName()}
 * Extension Version : {$reflectionExtension->getVersion()}
 */

{$constants}

{$functions}

{$classes}
EOF;

    }


    /**
     * @param \ReflectionFunction $reflectionFunction
     * @return string
     */
    protected function renderFunction(\ReflectionFunction $reflectionFunction)
    {
        $annotation = $this->renderFunctionAnnotation($reflectionFunction->getParameters());
        $fun        = sprintf("function %s (%s) {}",
            $reflectionFunction->getName(),
            $this->renderParameters($reflectionFunction->getParameters())
        );

        return $annotation . $fun;
    }

    /**
     * @param array $parameters
     * @return string
     */
    private function renderFunctionAnnotation(array $parameters)
    {
        $s = "/**\n";
        /** @var \ReflectionParameter $parameter */
        foreach ($parameters as $parameter) {
            $s .= sprintf(" * @param %s $%s\n", $parameter->getClass(), $parameter->getName());
        }
        $s .= " */\n";

        return $s;
    }

    /**
     * @param array $parameters
     * @return string
     */
    private function renderParameters(array $parameters)
    {
        $s = array();
        foreach ($parameters as $parameter) {
            $s[] = $this->renderParameter($parameter);
        }

        return implode(', ', $s);
    }

    /**
     * @param \ReflectionParameter $parameter
     * @return string
     */
    private function renderParameter(\ReflectionParameter $parameter)
    {
        if ($parameter->isArray()) {
            $s = 'array';
        } elseif ($parameter->isCallable()) {
            $s = 'callable';
        } else {
            $s = $parameter->getClass();
        }

        if ($s) {
            $s .= " ";
        }

        if ($parameter->isPassedByReference()) {
            $s .= '&';
        }

        $s .= "$" . $parameter->getName();

        try {
            if ($parameter->isDefaultValueConstant()) {
                $s .= sprintf(" = %s", var_export($parameter->getDefaultValueConstantName(), true));
            }
        } catch (\ReflectionException $e) {
        }
        try {
            if ($parameter->isDefaultValueAvailable()) {
                $s .= sprintf(" = %s", var_export($parameter->getDefaultValue(), true));
            }
        } catch (\ReflectionException $e) {
        }

        return $s;
    }


    /**
     * @param array $constants
     * @return string
     */
    private function renderConstants(array $constants)
    {
        $s = '';
        foreach ($constants as $key => $value) {
            $s .= sprintf("define('%s', %s);\n", $key, var_export($value, true));
        }

        return $s;
    }

    /**
     * @param array $functions
     * @return string
     */
    private function renderFunctions(array $functions)
    {
        $s = '';
        foreach ($functions as $function) {
            $s .= $this->renderFunction($function) . "\n";
        }

        return $s;
    }

    /**
     * @param array $classes
     * @return string
     */
    private function renderClasses(array $classes)
    {
        $s = '';
        foreach ($classes as $class) {
            $s .= $this->renderClass($class) . "\n";
        }

        return $s;
    }

    /**
     * @param \ReflectionClass $class
     * @return string
     */
    private function renderClass(\ReflectionClass $class)
    {
        $s = '';
        if ($class->isAbstract()) {
            $s .= "abstruct ";
        }
        if ($class->isFinal()) {
            $s .= "final ";
        }

        if ($class->isInterface()) {
            $s .= "interface \n";
        } else {
            $s .= "class \n";
        }

        $s .= "{\n";

        $s .= "}";


        return <<<EOF
namespaces {$class->getNamespaceName()}
{
{$s}
}
EOF;
    }


}