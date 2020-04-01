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

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;

use Composer\Console\Application as ComposerApp;
use Composer\Command\CreateProjectCommand;

use Symfony\Component\Console\Style\SymfonyStyle;

use Pho\Cli\Utils;

use VIPSoft\Unzip\Unzip;

class InitCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('init')
            ->setDescription('Initializes a new app');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $app_name = $io->ask('App Name (no dashes or spaces)');
        //error_log($app_name);
        $desc = $io->ask('Describe the app in a short sentence');
        $type = $io->choice('App Template', ['blank', 'basic', 'graphjs', 'twitter-simple', 'twitter-full', 'facebook']);
        
        $root = dirname(dirname(dirname(__DIR__)));
        $source = $root . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "skeleton";
        $destination = getcwd() . DIRECTORY_SEPARATOR . $app_name;
        @mkdir($destination);
        Utils::rcopy($source, $destination);
        $unzipper  = new Unzip();
        switch($type) {
            case "blank":
                
                break;
            case "basic":
                
                break;
            case "graphjs":
                
                break;
            case "twitter-simple":
                
                break;
            case "twitter-full":
                
                break;
            case "facebook":
                $this->downloadAndExtract('https://github.com/pho-recipes/Facebook/archive/master.zip');
                break;
        }
        
        $io->title("Project successfully built");
        $io->text([
            'Emre <href=https://symfony.com>Lorem</> ipsum dolor sit <options=bold,underscore>amet</>',
            'Consectetur adipiscing elit',
            'Aenean sit amet arcu <info>vitae</info> sem faucibus porta',
        ]);
        //$io->note('Xyz');
        $io->newLine(1);
        //$io->success('Lorem ipsum dolor sit amet'); // warning, error
        exit(0);
    }

}
