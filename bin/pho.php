#!/usr/bin/env php
<?php

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Console\Application;
use Pho\Cli;

$application = new Application('Pho', '0.1-dev');
$application->add(new Cli\HelloCommand());
$application->add(new Cli\UpdateCommand());
$application->run();
