<?php

namespace PrestaShop\PSTest\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use PrestaShop\Proc\ExecutableHelper;

class SetupCheck extends BaseCommand
{
    protected function configure()
    {
        $this
        ->setName('setup:check')
        ->setDescription('Check your setup for potential missing dependencies.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $programs = [
            'Required programs' => ['java', 'firefox', 'mysql', 'mysqldump'],
            'Optional programs' => ['Xvfb']
        ];

        foreach ($programs as $header => $progs) {
            $output->writeln("<options=underscore>$header</options=underscore>\n");
            foreach ($progs as $prog) {
                if (ExecutableHelper::inPath($prog)) {
                    $output->writeln(sprintf('<fg=green>✔ Found   : %s</fg=green>', $prog));
                } else {
                    $output->writeln(sprintf('<fg=red>✘ Missing : %s</fg=red>', $prog));
                }
            }
            $output->writeln('');
        }
    }
}
