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
    private $dot_phocli = "";

    protected function configure()
    {
        $this
            ->setName('init')
            ->setDescription('Initializes a new app');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->app_name = $this->io->ask('App Name (no dashes or spaces)', null,  function ($response) {
            if (empty($response)) {
                throw new \RuntimeException('App Name cannot be empty.');
            }
        
            return $response;
        });
        //error_log($this->app_name);
        $desc = $this->io->ask('Describe the app in a short sentence');
        $type = $this->io->choice('App Template', ['blank', 'basic', 'graphjs', 'twitter-simple', 'twitter-full', 'facebook', 'custom']);

        $source = $this->getSkeletonDir();

        $destination = getcwd() . DIRECTORY_SEPARATOR . $this->app_name;
        @mkdir($destination);
        Utils::rcopy($source, $destination);
        $this->createEnvFile($source, $destination);
        
        /* 
          On Linux phar can't extract dot files.
          That's why we "touch" suppressing any error message.
        */
        @touch($destination.DIRECTORY_SEPARATOR.".phonetworks"); 
        
        $unzipper  = new Unzip();
        
        $this->downloadRecipe($type);
        $this->setEnvFileParams($type, $destination);

        $this->io->title("Project successfully built");
        $this->io->text([
            'The project can be found at <options=bold,underscore>'.$destination.'</>'
            // 'Emre <href=https://symfony.com>Lorem</> ipsum dolor sit <options=bold,underscore>amet</>',
            // 'Consectetur adipiscing elit',
            // 'Aenean sit amet arcu <info>vitae</info> sem faucibus porta',
        ]);
        //$this->io->note('Xyz');
        $this->io->newLine(1);
        //$this->io->success('Lorem ipsum dolor sit amet'); // warning, error
        exit(0);
    }

    protected function createEnvFile(string $source, string $destination): void
    {
        if(file_exists($destination.DIRECTORY_SEPARATOR.".env.example")) {
            @copy($destination.DIRECTORY_SEPARATOR.".env.example", $destination.DIRECTORY_SEPARATOR.".env");
            return;
        }
        // Linux:
        @copy(dirname($source) . DIRECTORY_SEPARATOR . "env.example", $destination.DIRECTORY_SEPARATOR.".env");
        
    }

    /**
     * Helper method to fetch the skeleton directory
     * 
     * Works no matter if it's called from within the phar or not.
     * 
     * @return string The skeleton dir
     */
    protected function getSkeletonDir(): string
    {
        $root = dirname(dirname(dirname(__DIR__)));
        $phar = \Phar::running();
        if(empty($phar))
            return $root . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "skeleton";
        $archive = new \Phar($phar);
        $tmp = Utils::createTempDir();
        $archive->extractTo($tmp);
        return $tmp . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "skeleton";
    }

    /**
     * Sets the .env file with founder and graph classes.
     * 
     * The areas to change in the .env file:
     *  GRAPH_CLASS=""
     *  FOUNDER_CLASS=""
     *  USER_CLASS=""
     *  FOUNDER_PARAMS=""
     * 
     * @param string $type Project type (e.g. facebook, blank)
     * @param string $destination where the .env file sits
     * 
     * @return void
     */
    protected function setEnvFileParams(string $type, string $destination): void
    {
        $env_file = $destination . DIRECTORY_SEPARATOR . ".env";
        $contents = file_get_contents($env_file);
        $username = $this->io->ask('Founder Username', "admin", function ($response) {
            if (empty($response)) {
                throw new \RuntimeException('Username cannot be empty.');
            }
        
            return $response;
        });
        $password = $this->io->askHidden('Founder Password',  function ($response) {
            if (empty($response)) {
                throw new \RuntimeException('Password cannot be empty.');
            }
        
            return $response;
        });
        $email = "";
        $params = "{$username}::{$password}";
        $graph_class = "";
        $founder_class = "\\PhoNetworksAutogenerated\\User";
        switch($type) {
            case "blank":
            case "basic":
                $graph_class = "\\PhoNetworksAutogenerated\\Graph";
                break;
            case "graphjs":
                $graph_class = "\\PhoNetworksAutogenerated\\Site";
                $email = $this->io->ask('Founder Email');
                $params = "{$username}::{$email}::{$password}";
                break;
            case "twitter-simple":
            case "twitter-full":
                $graph_class = "\\PhoNetworksAutogenerated\\Twitter";
                break;
            case "facebook":
                $graph_class = "\\PhoNetworksAutogenerated\\Facebook";
                break;
            case "custom":
                $email = $this->io->ask('Founder Email');
                $contents = $this->dot_phocli;
                $contents = str_replace("{username}", $username, $contents);
                $contents = str_replace("{password}", $password, $contents);
                $contents = str_replace("{email}", $email, $contents);
                file_put_contents($env_file, "\n\n".$contents, LOCK_EX|FILE_APPEND);
                return; // DO NOT CONTINUE
        }
        
        $founder_class = addslashes($founder_class);
        $graph_class = addslashes($graph_class);

        $contents = str_replace("GRAPH_CLASS=\"\"", sprintf("GRAPH_CLASS=\"%s\"", $graph_class), $contents);
        $contents = str_replace("FOUNDER_CLASS=\"\"", sprintf("FOUNDER_CLASS=\"%s\"", $founder_class), $contents);
        $contents = str_replace("USER_CLASS=\"\"", sprintf("USER_CLASS=\"%s\"", $founder_class), $contents);
        $contents = str_replace("FOUNDER_PARAMS=\"\"", sprintf("FOUNDER_PARAMS=\"%s\"", $params), $contents);
        
        file_put_contents($env_file, $contents, LOCK_EX);

    }

    /**
     * Downloads the recipe given the project type
     * 
     * @param string $type Project type (e.g. facebook, blank)
     * 
     * @return void
     */
    protected function downloadRecipe(string $type): void
    {
        switch($type) {
            case "blank":
                $this->createBlankProject();
                break;
            case "basic":
                $this->downloadAndExtract('https://github.com/pho-recipes/Basic/archive/master.zip');
                break;
            case "graphjs":
                $this->downloadAndExtract('https://github.com/pho-recipes/Web/archive/master.zip');
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
            case "custom":
                $tarball = $this->io->ask('Custom Project\'s Path or Github URL');
                if(preg_match('/^https\:\/\/github\.com\/[^\/]+\/[^\/]+/', $tarball)) {
                    $tarball .= "/archive/master.zip";
                    $this->downloadAndExtract($tarball);
                }
                elseif(file_exists($tarball)) {
                    $this->extract($tarball);
                }
                else {
                    $this->io->note('Not a valid repo. Switching to the Basic recipe.');
                    $this->downloadRecipe("basic");
                    return;
                }
                
                break;

        }
    }

    /**
     * Extracts the project zip to destination
     * 
     * @param string $fileDestination
     * 
     * @return void
     */
    protected function extract(string $fileDestination): void
    {
        $fileDestinationEx = Utils::createTempDir();
        if(!$fileDestinationEx) {
            $this->io->text(['Unable to Create Project (not able to create temp folder)']);
            exit(0);
        }

        // Extract file to a temp folder
        $unzipper  = new Unzip();
        $filenames = $unzipper->extract($fileDestination, $fileDestinationEx);
        $tmpdir_contents = glob($fileDestinationEx."/*");
        $tmpdir = $tmpdir_contents[0];

        $this->dot_phocli = $tmpdir.DIRECTORY_SEPARATOR.".phocli";
        if(file_exists($this->dot_phocli)) {
            $this->dot_phocli = file_get_contents($this->dot_phocli);
        }
        else {
            $this->dot_phocli = "";
        }
            
        // then put the pgql files into schema/ folder
        $files = glob($fileDestinationEx . '/*/*.pgql');
        if(!is_dir($this->app_name . '/schema/')) {
            mkdir($this->app_name . '/schema/');
            mkdir($this->app_name . '/schema/Nodes/');
            mkdir($this->app_name . '/schema/Edges/');
        }
        foreach ($files as $file) {
            $fileNameSplit = explode("/", $file);
            $fileName = end($fileNameSplit);
            rename($file,   $this->app_name . '/schema/' . $fileName);
        }
        Utils::rcopy($tmpdir.DIRECTORY_SEPARATOR."Nodes", $this->app_name . '/schema/Nodes');
        Utils::rcopy($tmpdir.DIRECTORY_SEPARATOR."Edges", $this->app_name . '/schema/Edges');
    
        // .compiled folder into build/ folder
        $files = glob($fileDestinationEx . '/*/.compiled');
        if(!is_dir($this->app_name . '/build/')) {
            mkdir($this->app_name . '/build/');
        }
            
        foreach ($files as $file) {
            Utils::rcopy ($file,   $this->app_name . '/build/');
        }
        // delete the zip and temp directory
        unlink($fileDestination);
        Utils::dirDel($fileDestinationEx);
    }

    /**
     * Downloads the given Recipe from Github and extracts it to destination
     * 
     * @param string $urlToDownload
     * 
     * @return void
     */
    protected function downloadAndExtract(string $urlToDownload): void
    {
        $this->io->text(['Building your Project...']);
        $fileDestination = $this->app_name."/tmpfile.zip";
        // $fileDestinationEx = $this->app_name."/pho-project-tmpfolder";
        
        
        // Download file to our app
        file_put_contents($fileDestination, fopen($urlToDownload, 'r'));
            
        $this->extract($fileDestination);

    }

    /**
     * Create a blank Pho project
     * 
     * @return void
     */
    protected function createBlankProject(): void 
    {
        $this->io->text(['Building your Project...']);
        if(!is_dir($this->app_name . '/schema/')) {
            mkdir($this->app_name . '/schema/');
        }
        if(!is_dir($this->app_name . '/build/')) {
            mkdir($this->app_name . '/build/');
        }
    }
}
