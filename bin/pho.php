#!/usr/bin/env php
<?php

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Console\Application;
use Pho\Cli;

$application = new Application('Pho', '0.1-dev');
$application->add(new Cli\BuildCommand());
$application->add(new Cli\UpdateCommand());
$application->add(new Cli\InitCommand());
$application->add(new Cli\ServeCommand());
$application->run();
