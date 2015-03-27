<?php

namespace PrestaShop\TestRunner\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use PrestaShop\TestRunner\CLIRunner;

class TestRun extends Command
{
    protected function configure()
    {
        $this->setName('test:run')->setDescription('Run a test or a group of tests.')
             ->addArgument('path', InputArgument::REQUIRED, 'Which test?')
             ->addOption('parallel', 'p', InputOption::VALUE_REQUIRED, 'How many tests to run in parallel?', 1)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output; // shut the linter up
        $path =  $input->getArgument('path');

        $runner = new CLIRunner();

        $runner->setMaxWorkers($input->getOption('parallel'));

        $runner->setOutputInterface($output);
        $runner->addTestPath($path);

        $runner->run();
    }
}
