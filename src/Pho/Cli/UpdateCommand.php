<?php

namespace Pho\Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Humbug\SelfUpdate\Updater;


class UpdateCommand extends Command
{

    private $_urlToGithubPagesPharFile = "https://phonetworks.github.io/pho-cli/pho.phar";
    private $_urlToGithubPagesVersionFile = "https://phonetworks.github.io/pho-cli/pho.phar.version";

    protected function configure()
    {
        $this
            ->setName('update')
            ->setDescription('Self updates the app.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $updater = new Updater(null, false);
        $updater->getStrategy()->setPharUrl($this->_urlToGithubPagesPharFile);
        $updater->getStrategy()->setVersionUrl($this->_urlToGithubPagesVersionFile);
        try {
            $result = $updater->update();
            if (! $result) {
                // No update needed!
                $output->writeln('<comment>It\'s already up to date.</comment>');
                exit(0);
            }
            $new = $updater->getNewVersion();
            $old = $updater->getOldVersion();
            $output->writeln(sprintf('<info>Updated from %s to %s</info>', $old, $new));
            exit(0);
        } catch (\Exception $e) {
            // Report an error!
            $output->writeln('<error>There was an error!</error>');
            exit(1);
        }
    }
}
