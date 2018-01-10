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

class ServeCommand extends Command
{

    protected $server, $kernel, $founder;

    protected function configure()
    {
        $this
            ->setName('serve')
            ->setDescription('Run server based on pho Graph ')
            ->addArgument('kernel', 'k', InputOption::VALUE_OPTIONAL, 'Kernel path');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $kernel_path = $input->getOption('kernel');

        if (empty($kernel_path) || !is_dir($kernel_path)) {
            var_dump($kernel_path);
            throw new \InvalidArgumentException('Kernel path not exists;');
            return;
        }

        //include kernel classes for pho server
        include_once rtrim($kernel_path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

        if (!class_exists('\\Pho\\Kernel\\Kernel')) {
            throw new \InvalidArgumentException('Kernel class not founded in current path:' . $kernel_path);
            return;
        }
        
        include(rtrim($kernel_path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . "kernel.php");

        $this->server = new \Pho\Server\Rest\Daemon($kernel);
        //eval(\Psy\sh());
        $port = (!empty(getenv('SERVER_PORT'))) ? getenv('SERVER_PORT') : 1337;
        $host = (!empty(getenv('SERVER_HOST'))) ? getenv('SERVER_HOST') : '0.0.0.0';
        $this->server->setPort($port);
        $this->server->serve($port, $host);
        
    }
}
