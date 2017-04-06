<?php

namespace Pho\Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HelloCommand extends Command {

	protected function configure()
    {
        $this
            ->setName('hello')
            ->setDescription('Say hello')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Hello World');
					// green text
				$output->writeln('<info>foo</info>');

				// yellow text
				$output->writeln('<comment>foo</comment>');

				// black text on a cyan background
				$output->writeln('<question>foo</question>');

				// white text on a red background
				$output->writeln('<error>foo</error>');

				// bold text on a yellow background
				$output->writeln('<bg=yellow;options=bold;fg=black>foo</>');
    }
}
