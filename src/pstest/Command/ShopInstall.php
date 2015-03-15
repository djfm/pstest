<?php

namespace PrestaShop\PSTest\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use PrestaShop\PSTest\SystemSettings;
use PrestaShop\PSTest\LocalShopSourceSettings;
use PrestaShop\PSTest\Shop\LocalShopFactory;

class ShopInstall extends Command
{
    private $configuration_file_name = 'pstest.settings.json';


    protected function configure()
    {
        $this
        ->setName('shop:install')
        ->setDescription('Install the shop');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $systemSettings = new SystemSettings();
        $sourceSettings = new LocalShopSourceSettings();

        if (!file_exists($this->configuration_file_name)) {
            $output->writeln(sprintf('<error>Oops:</error> Cannot find configuration file `%s` in current directory.', $this->configuration_file_name));
            return 1;
        }

        $systemSettings->loadFile($this->configuration_file_name);
        $sourceSettings->loadFile($this->configuration_file_name);

        $shopFactory = new LocalShopFactory($systemSettings, $sourceSettings);

        $shop = $shopFactory->makeShop();

        return 0;
    }
}
