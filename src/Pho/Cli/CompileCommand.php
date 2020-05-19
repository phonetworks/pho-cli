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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

use Pho\Compiler\Compiler;

class CompileCommand extends Command
{

    protected $compiler, $extension;

    protected function configure()
    {
        $this
            ->setName('compile')
            ->setDescription('[! Advanced ] Compiles GraphQL schema into executable Pho')
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

        $this->compiler = new Compiler();
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


    /**
     * Create zip archive from folder where is placed source
     * @param  string $sorucre     [description]
     * @param  string $destination [description]
     * @return [type]              [description]
     */
    private function createZip(string $source, string $destination): string 
    {
        $zip = new \ZipArchive();
        $filename = $destination.DIRECTORY_SEPARATOR.'file_'.time().'.zip';
        
        $zip->open($filename, \ZipArchive::CREATE);
        
        // Create recursive directory iterator
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );
        $regex = '/\.'.$this->extension.'$/';
        $ext_files = new \RegexIterator($files, $regex);
        
        foreach ($ext_files as $name => $file)
        {
            // Skip directories (they would be added automatically)
            if (!$file->isDir())
            {
                // Get real and relative path for current file
                $filePath = $file->getRealPath();
                $relativePath = ltrim(str_replace($source, '', $filePath), DIRECTORY_SEPARATOR);
                // Add current file to archive
                $zip->addFile($filePath, $relativePath);
            }
        }
        // Zip archive will be created only after closing object
        $zip->close();

        return $filename;
    }
}