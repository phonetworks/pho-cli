<?php

namespace Pho\Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BuildCommand extends Command {

	protected function configure()
    {
        $this
            ->setName('build')
            ->setDescription('Builds graphql schema into executable Pho')
            ->addArgument('source', InputArgument::OPTIONAL, 'The directory where the graphql schema resides.')
            ->addArgument('destination', InputArgument::OPTIONAL, 'The directory where the compiled Pho files will go.')
            ->addArgument('extension', InputArgument::OPTIONAL, 'The extension to scan for graphql schema files.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $source = $input->getArgument('source');
        $destination = $input->getArgument('destination');
        $destination = $input->getArgument('extension');
        if(empty($source)) 
            $source = 'schema';
        if(empty($destination)) 
            $destination = 'compiled';
        if(empty($extension)) 
            $extension = 'pgql';
        if(!file_exists($source)) {            
            $output->writeln(sprintf('<error>The schema directory "%s" does not exist or is inaccessible.</error>', $source));
            exit(1);
        }
        mkdir($destination);
        if(!file_exists($destination)) {            
            $output->writeln(sprintf('<error>The compilation directory "%s" is inaccessible.</error>', $destination));
            exit(1);
        }

        $dir = scandir($source);
        $compiler = new \Pho\Compiler\Compiler();
        foreach($dir as $file) {
            if(substr($file, -1 * (strlen(".".$extension)) ) == ".".$extension) {
                $compiler->compile($source."/".$file)->save(
                    $destination."/".str_replace(".".$extension, "", $file)
                );
            }
        }

        try {
            \Pho\Compiler\Inspector::assertParity($destination);
        }
        catch(\Pho\Compiler\Exceptions\ImpairedStackException $e) {

        }

    }
}
