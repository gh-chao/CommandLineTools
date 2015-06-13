#!/usr/bin/env php
<?php


use DbDocCreator\DbDocCreatorCommand;
use PHPExtensionDocCreator\PHPExtensionDocCreatorCommand;

include dirname(__DIR__) . '/vendor/autoload.php';

$console = new \Console\Application();
$console->add(new DbDocCreatorCommand());
$console->add(new PHPExtensionDocCreatorCommand());

$console->run();

