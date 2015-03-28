<?php

namespace PrestaShop\PSTest\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use PrestaShop\PSTest\SystemSettings;
use PrestaShop\PSTest\LocalShopSourceSettings;
use PrestaShop\PSTest\Shop\DefaultSettings;

class ConfigCreate extends BaseCommand
{
    protected function configure()
    {
        $this
        ->setName('config:create')
        ->setDescription('Create a configuration file with the default values in the current directory')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $this->getConfigurationFileName();

        $settingsInstances = [new SystemSettings, new LocalShopSourceSettings, new DefaultSettings];

        if (file_exists($path)) {
            foreach($settingsInstances as $settings) {
                $settings->loadFile($path);
            }
        }

        foreach($settingsInstances as $settings) {
            $settings->dumpFile($path);
        }
    }
}
