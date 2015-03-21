<?php

namespace PrestaShop\TestRunner\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use PrestaShop\TestRunner\Runner;

class TestRun extends Command
{
    protected function configure()
    {
        $this->setName('test:run')->setDescription('Run a test or a group of tests.')
             ->addArgument('path', InputArgument::REQUIRED, 'Which test?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output; // shut the linter up
        $path =  $input->getArgument('path');

        $runner = new Runner();

        $runner->setOutputInterface($output);
        $runner->addTestPath($path);

        $runner->run();
    }
}
