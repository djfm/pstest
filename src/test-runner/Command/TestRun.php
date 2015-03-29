<?php

namespace PrestaShop\TestRunner\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use PrestaShop\TestRunner\CLIRunner;
use PrestaShop\TestRunner\RunnerPlugin;

class TestRun extends Command
{
    private $pluginOptionNames = [];

    protected function configure()
    {
        $this->setName('test:run')->setDescription('Run a test or a group of tests')
             ->addArgument('path', InputArgument::REQUIRED, 'Which test?')
             ->addOption('parallel', 'p', InputOption::VALUE_REQUIRED, 'How many tests to run in parallel?', 1)
             ->addOption('info', 'i', InputOption::VALUE_NONE, 'Only display information about the tests that would be ran, don\'t run them')
             ->addOption('filter', 'f', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Filter tests to run', [])
        ;
    }

    protected function addRunnerPlugin(RunnerPlugin $plugin)
    {
        $options = $plugin->getCLIOptions();

        $this->pluginOptionNames[get_class($plugin)] = [];

        foreach ($options as $option) {
            $this->addOption(
                $option->getName(),
                $option->getShortcut(),
                $option->getMode(),
                $option->getDescription(),
                $option->getDefault()
            );

            $this->pluginOptionNames[get_class($plugin)][] = $option->getName();
        }


        return $this;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output; // shut the linter up
        $path =  $input->getArgument('path');

        $pluginOptions = [];

        foreach ($this->pluginOptionNames as $className => $optionNames) {
            $pluginOptions[$className] = [];

            foreach ($optionNames as $optionName) {
                $pluginOptions[$className][$optionName] = $input->getOption($optionName);
            }
        }

        $runner = new CLIRunner();

        $runner->setPluginOptions($pluginOptions);
        $runner->setInformationOnly($input->getOption('info'));
        $runner->setFilters($input->getOption('filter'));

        $runner->setMaxWorkers($input->getOption('parallel'));

        $runner->setOutputInterface($output);
        $runner->addTestPath($path);

        $runner->run();
    }
}
