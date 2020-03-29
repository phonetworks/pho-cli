<?php

/*
 * This file is part of the Pho package.
 *
 * (c) Emre Sokullu <emre@phonetworks.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pho\Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;

use Composer\Console\Application as ComposerApp;
use Composer\Command\CreateProjectCommand;

class GenerateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('generate')
            ->setDescription('Generate a server based on given Pho kernel')
            ->addArgument('type', InputOption::VALUE_REQUIRED, 'Server type; the only option "rest" for the time being')
            ->addArgument('kernel', InputOption::VALUE_REQUIRED, 'Kernel path')
            ->addArgument('destination', InputArgument::OPTIONAL, 'The directory where the generated files will go.');
    }

    protected function buildRest(InputInterface $input, OutputInterface $output)
    {

    }

    protected function buildKernel(InputInterface $input, OutputInterface $output)
    {
        // pho-server-rest
        $kernel = $input->getArgument("kernel");
        $destination = $input->getArgument('destination'); 
        @mkdir($destination);

        $root = sprintf("%s%sdata%s", dirname(dirname(dirname(__DIR__))), DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR);
        
        $composer_source = sprintf("%scomposer.json.sample", $root);
        $composer_contents = \file_get_contents($composer_source);
        $composer_file = $destination.DIRECTORY_SEPARATOR."composer.json";
        file_put_contents($composer_file, $composer_contents);

        $run_source = sprintf("%srun.php.sample", $root);
        $run_contents = file_get_contents($run_source);
        $run_file = $destination.DIRECTORY_SEPARATOR."run.php";
        file_put_contents($run_file, $run_contents);

        $kernel_destination = $destination.DIRECTORY_SEPARATOR."kernel";
        Utils::rcopy($kernel, $kernel_destination);

        $output->writeln(sprintf("<info>Server files can be found at: %s</info>", $destination));
        $output->writeln("<comment>To get started: </comment>");
        $output->writeln("<comment>1) Run \"composer install\"</comment>");
        $output->writeln("<comment>2) Edit the .env.example as per your configuration</comment>");
        $output->writeln("<comment>3) Move .env.example to .env</comment>");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $type = ucfirst(strtolower($input->getArgument("type")));
        switch($type) {
            case "Rest":
            case "Kernel":
                $op = sprintf("build%s", $type);
                $this->$op($input, $output);
                break;
        }
        
    }
}