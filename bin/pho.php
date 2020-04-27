#!/usr/bin/env php
<?php

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Console\Application;
use Pho\Cli;

$version = file_get_contents(dirname(__DIR__) . DIRECTORY_SEPARATOR . "VERSION");
$application = new Application('Pho', $version);
$application->add(new Cli\BuildCommand());
$application->add(new Cli\InitCommand());
//$application->add(new Cli\ServeCommand());
//$application->add(new Cli\RunCommand());
$application->run();
