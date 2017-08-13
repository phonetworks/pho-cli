<?php

namespace Pho\Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BuildCommand extends Command
{

    protected $compiler, $extension;

    protected function configure()
    {
        $this
            ->setName('build')
            ->setDescription('Builds graphql schema into executable Pho')
            ->addArgument('source', InputArgument::OPTIONAL, 'The directory where the graphql schema resides.')
            ->addArgument('destination', InputArgument::OPTIONAL, 'The directory where the compiled Pho files will go.')
            ->addArgument('extension', InputArgument::OPTIONAL, 'The extension to scan for graphql schema files.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $source = $input->getArgument('source');
        $destination = $input->getArgument('destination');
        $this->extension = $input->getArgument('extension');
        if(empty($source)) { 
            $source = 'schema';
        }
        if(empty($destination)) { 
            $this->destination = 'compiled';
        }
        if(empty($extension)) { 
            $this->extension = 'pgql';
        }
        if(!file_exists($source)) {            
            $output->writeln(sprintf('<error>The schema directory "%s" does not exist or is inaccessible.</error>', $source));
            exit(1);
        }
        @mkdir($destination);
        if(!file_exists($destination)) {            
            $output->writeln(sprintf('<error>The compilation directory "%s" is inaccessible.</error>', $destination));
            exit(1);
        }

        
        $this->compiler = new \Pho\Compiler\Compiler();
        $this->processDir($source);
        
        $this->compiler->save($destination);
        $output->writeln(sprintf('<info>Project successfully built at: %s</info>', $destination));
        exit(0);

    }

    protected function processDir(string $source): void 
    {
        $dir = scandir($source);
        foreach($dir as $file) {
            if($file[0]==".") { // includes hidden, . and ..
                continue;
            }
            elseif(is_dir($source.DIRECTORY_SEPARATOR.$file)) {
                $this->processDir($source.DIRECTORY_SEPARATOR.$file);
            }
            elseif(substr($file, -1 * (strlen(".".$this->extension))) == ".".$this->extension) {
                $this->compiler->compile($source.DIRECTORY_SEPARATOR.$file);
            }
        }
    }
}
