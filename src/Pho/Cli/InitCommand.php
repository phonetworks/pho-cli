<?php

namespace Pho\Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class InitCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('init')
            ->setDescription('Initializes a new project')
            ->addArgument('dir', InputArgument::REQUIRED, 'The directory where the application will be hosted.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dir = $input->getArgument('dir');
        if(file_exists($dir)) {
            $output->writeln(sprintf('<error>The directory "%s" already exists.</error>', $dir));
            exit(1);
        }
        mkdir($dir);
        $process = new Process(
            sprintf('cd %s && '.dirname(__FILE__).'/../../../vendor/bin/composer require phonetworks/pho-kernel "@dev"', $dir)
        );
        
        //$process->run();
        //if (!$process->isSuccessful()) throw new ProcessFailedException($process);
        //echo $process->getOutput();
        
        $process->start();

        foreach ($process as $type => $data) {
            if ($process::OUT === $type) {
                $output->write($data);
            } else { // $process::ERR === $type
                $output->write($data);
            }
        }

        mkdir($dir."/schema");

        $output->writeln("<info>Project initialized.</info>");
        $output->writeln("<comment>You may change the schema files of your project from ...</comment>");
        $output->writeln("<comment>Otherwise just run build to compile your files.</comment>");
        exit(0);

    }
}
