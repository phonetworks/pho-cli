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
use Symfony\Component\Filesystem\Filesystem;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

use Symfony\Component\Console\Style\SymfonyStyle;

class BuildCommand extends Command
{

    
    protected $compiler, $extension;

    protected function configure()
    {
        $this
            ->setName('build')
            ->setDescription('Builds the GraphQL schema');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Utils::checkPhoDir($input, $output);
        $this->compile($input, $output);
        exit(0);
    }


    protected function compile(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $source = getcwd() . DIRECTORY_SEPARATOR . "schema";
        $destination = getcwd() . DIRECTORY_SEPARATOR . "build";
        $this->extension = "pgql";

        if(!file_exists($source)) { 
            $io->error(sprintf('Schema directory "%s" is inaccessible', $source));
            exit(1);
        }

        @mkdir($destination);
        if(!file_exists($destination)) {
            $io->error(sprintf('Build directory "%s" is inaccessible', $destination));
            exit(1);
        }

        $this->compiler = new \Pho\Compiler\Compiler();
        $this->processDir($source);
        $this->compiler->save($destination);

        $io->success(sprintf('Project successfully built at %s', $destination));

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
