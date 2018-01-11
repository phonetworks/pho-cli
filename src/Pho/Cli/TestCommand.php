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

use Composer\Console\Application as ComposerApp;
use Symfony\Component\Console\Input\ArrayInput;

use Symfony\Component\Console\Question\ConfirmationQuestion;

class TestCommand extends Command
{

    protected $server, $kernel, $founder;

    protected function configure()
    {
        $this
            ->setName('test')
            ->setDescription('Test RESTful HTTP server based on given Pho kernel -- uses vagrant')
            ->addArgument('kernel', InputOption::VALUE_REQUIRED, 'Kernel path');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $kernel_path = $input->getArgument('kernel');

        if(is_array($kernel_path)) {
            $kernel_path = $kernel_path[0];
        }
        
        if (empty($kernel_path) || !is_dir($kernel_path)) {
            var_dump($kernel_path);
            throw new \InvalidArgumentException('Kernel path not exists;');
            return;
        }

        $new_dir = (glob(__DIR__ . "/../../../data"))[0];

        if(!Utils::dirEmpty($new_dir)) {
            $helper = $this->getHelper('question');
            $question = new ConfirmationQuestion('Data directory must be empty. Is it OK to erase?', false);
            if (!$helper->ask($input, $output, $question)) {
                return;
            }
            Utils::dirDel($new_dir);
        }

        Utils::rcopy(rtrim($kernel_path, DIRECTORY_SEPARATOR), $new_dir);
        $autoload_file = $new_dir . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
        if(!file_exists($autoload_file)) {
            $input = new ArrayInput(array(
                'command' => 'install',
                'directory' => $new_dir
                 ));
    
            //Create the application and run it with the commands
            $composer = new ComposerApp();
            $composer->setAutoExit(false); 
            $composer->run($input);
        }

        //include kernel classes for pho server
        include_once $autoload_file;

        if (!class_exists('\\Pho\\Kernel\\Kernel')) {
            throw new \InvalidArgumentException('Kernel class cannot be found in current path:' . $new_dir);
            return;
        }

        $kernel_file = $new_dir . DIRECTORY_SEPARATOR . 'kernel.php';

        if(!file_exists($kernel_file)) {
            throw new \InvalidArgumentException('Kernel file cannot be found in current path:' . $kernel_file);
            return;
        }
        
        $output->writeln("<info>Initializing vagrant. Please wait as this may take a while.</info>");
        `vagrant up`;
        $output->writeln("<info>Vagrant started. You may use port 8000 on your machine to query your kernel via HTTP.</info>");
        
    }
}
