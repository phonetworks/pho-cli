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
    private $io;
    private $app_name;
    protected function configure()
    {
        $this
            ->setName('init')
            ->setDescription('Initializes a new app');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->app_name = $this->io->ask('App Name (no dashes or spaces)');
        //error_log($this->app_name);
        $desc = $this->io->ask('Describe the app in a short sentence');
        $type = $this->io->choice('App Template', ['blank', 'basic', 'graphjs', 'twitter-simple', 'twitter-full', 'facebook']);
        
        $root = dirname(dirname(dirname(__DIR__)));
        $source = $root . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "skeleton";
        $destination = getcwd() . DIRECTORY_SEPARATOR . $this->app_name;
        @mkdir($destination);
        Utils::rcopy($source, $destination);
        $unzipper  = new Unzip();
        switch($type) {
            case "blank":
                $this->createBlankProject();
                break;
            case "basic":
                $this->downloadAndExtract('https://github.com/pho-recipes/Basic/archive/master.zip');
                break;
            case "graphjs":
                $this->downloadAndExtract('https://github.com/pho-recipes/Basic/archive/master.zip');
                break;
            case "twitter-simple":
                $this->downloadAndExtract('https://github.com/pho-recipes/Twitter-simple/archive/master.zip');
                break;
            case "twitter-full":
                $this->downloadAndExtract('https://github.com/pho-recipes/Twitter-full/archive/master.zip');
                break;
            case "facebook":
                $this->downloadAndExtract('https://github.com/pho-recipes/Facebook/archive/master.zip');
                break;
        }
        
        $this->io->title("Project successfully built");
        $this->io->text([
            'Emre <href=https://symfony.com>Lorem</> ipsum dolor sit <options=bold,underscore>amet</>',
            'Consectetur adipiscing elit',
            'Aenean sit amet arcu <info>vitae</info> sem faucibus porta',
        ]);
        //$this->io->note('Xyz');
        $this->io->newLine(1);
        //$this->io->success('Lorem ipsum dolor sit amet'); // warning, error
        exit(0);
    }
    protected function downloadAndExtract($urlToDownload){
        $this->io->text(['Building your Project...']);
        $fileDestination = $this->app_name."/tmpfile.zip";
        $fileDestinationEx = $this->app_name."/tmpfolder";
        // Download file to our app
        file_put_contents($fileDestination, fopen($urlToDownload, 'r'));
        // Extract file to a temp folder
        $unzipper  = new Unzip();
        $filenames = $unzipper->extract($fileDestination, $fileDestinationEx);
        
        // then put the pgql files into schema/ folder
        $files = glob($fileDestinationEx . '/*/*.pgql') + glob($fileDestinationEx . '/*/*/*.pgql');
        if(!is_dir($this->app_name . '/schema/')) {
            mkdir($this->app_name . '/schema/');
        }
        foreach ($files as $file) {
            $fileNameSplit = explode("/", $file);
            $fileName = end($fileNameSplit);
            rename($file,   $this->app_name . '/schema/' . $fileName);
        }

        // .compiled folder into build/ folder
        $files = glob($fileDestinationEx . '/*/.compiled');
        if(!is_dir($this->app_name . '/build/')) {
            mkdir($this->app_name . '/build/');
        }
        foreach ($files as $file) {
            $fileNameSplit = explode("/", $file);
            $fileName = end($fileNameSplit);
            rename($file,   $this->app_name . '/build/' . $fileName);
        }
        // delete the zip and temp directory
        unlink($fileDestination);
        Utils::dirDel($fileDestinationEx);
    }
    protected function createBlankProject() {
        $this->io->text(['Building your Project...']);
        if(!is_dir($this->app_name . '/schema/')) {
            mkdir($this->app_name . '/schema/');
        }
        if(!is_dir($this->app_name . '/build/')) {
            mkdir($this->app_name . '/build/');
        }
    }
}
