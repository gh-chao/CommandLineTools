#!/usr/bin/env php
<?php

use DbDocCreator\Command\DbDocCreator;
use PHPExtensionDocCreator\Command\PHPExtensionDocCreator;

include dirname(__DIR__) . '/vendor/autoload.php';

$console = new \Console\Application();
$console->add(new DbDocCreator());
$console->add(new PHPExtensionDocCreator());

$console->run();

