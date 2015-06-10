<?php
namespace Console;


use Console\Helper\QuestionHelper;
use Symfony\Component\Console\Helper\DebugFormatterHelper;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\ProcessHelper;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Console\Helper\TableHelper;

class Application extends \Symfony\Component\Console\Application
{

    protected function getDefaultHelperSet()
    {
        return new HelperSet(array(
            new FormatterHelper(),
            new DialogHelper(false),
            new ProgressHelper(false),
            new TableHelper(false),
            new DebugFormatterHelper(),
            new ProcessHelper(),
            new QuestionHelper(),
        ));
    }
}