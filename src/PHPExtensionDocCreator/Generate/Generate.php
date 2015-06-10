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



    }



    /**
     * @param array $constants
     * @return string
     */
    private function renderConstants(array $constants)
    {
        $s = '';
        foreach ($constants as $key => $value) {
            $s .= sprintf("define('%s', '%s');\n", $key, $value);
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
    }



}