<?php

namespace Pho\Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Humbug\SelfUpdate\Updater;


class UpdateCommand extends Command {

  private $urlToGithubPagesPharFile = "https://phonetworks.github.io/pho-cli/pho.phar";
  private $urlToGithubPagesVersionFile = "https://phonetworks.github.io/pho-cli/pho.phar.version";

	protected function configure()
    {
        $this
            ->setName('update')
            ->setDescription('Self updates the app.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
      $updater = new Updater();
      $updater->getStrategy()->setPharUrl($this->urlToGithubPagesPharFile);
      $updater->getStrategy()->setVersionUrl($this->urlToGithubPagesVersionFile);
      try {
        $result = $updater->update();
        if (! $result) {
            // No update needed!
            exit(0);
        }
        $new = $updater->getNewVersion();
        $old = $updater->getOldVersion();
        printf('Updated from %s to %s', $old, $new);
        exit(0);
      } catch (\Exception $e) {
        // Report an error!
        exit(1);
      }
    }
}
