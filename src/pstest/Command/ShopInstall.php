<?php

namespace PrestaShop\PSTest\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use PrestaShop\PSTest\SystemSettings;
use PrestaShop\PSTest\Shop\LocalShopSourceSettings;
use PrestaShop\PSTest\Shop\LocalShopFactory;

class ShopInstall extends Command
{
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

        $shopFactory = new LocalShopFactory($systemSettings, $sourceSettings);
    }
}
