<?php

namespace PrestaShop\PSTest\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use PrestaShop\Selenium\SeleniumServerFactory;
use PrestaShop\Selenium\SeleniumBrowserFactory;
use PrestaShop\Selenium\SeleniumBrowserSettings;
use PrestaShop\Selenium\Xvfb\XvfbServerFactory;

use PrestaShop\PSTest\SystemSettings;
use PrestaShop\PSTest\LocalShopSourceSettings;
use PrestaShop\PSTest\Shop\LocalShopFactory;

class ShopInstall extends BaseCommand
{
    protected function configure()
    {
        $this
        ->setName('shop:install')
        ->setDescription('Install the shop')
        ->addOption('headless', null, InputOption::VALUE_NONE, 'Try to perform the installation headlessly.');
        ;
    }

    private function getBrowserFactory($headless = false)
    {
        $ssf = new SeleniumServerFactory;
		$xsf = new XvfbServerFactory;

        $xvfbServer = null;

		if ($headless && $xsf->hasXvfbProgram()) {
			$xvfbServer = $xsf->makeServer();
			$ssf->setXvfb($xvfbServer);
		}

		$seleniumServer = $ssf->makeServer();
		$browserSettings = new SeleniumBrowserSettings;
		$browserFactory = new SeleniumBrowserFactory(
            $seleniumServer->getSettings(),
            $browserSettings
        );

        register_shutdown_function(function () use ($seleniumServer, $xvfbServer, $browserFactory) {

            $browserFactory->quitLaunchedBrowsers();

            $seleniumServer->shutDown();
            if ($xvfbServer) {
                $xvfbServer->shutDown();
            }
        });

        return $browserFactory;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $systemSettings = new SystemSettings();
        $sourceSettings = new LocalShopSourceSettings();

        if (!file_exists($this->getConfigurationFileName())) {
            $output->writeln(sprintf('<error>Oops:</error> Cannot find configuration file `%s` in current directory.', $this->getConfigurationFileName()));
            return 1;
        }

        $systemSettings->loadFile($this->getConfigurationFileName());
        $sourceSettings->loadFile($this->getConfigurationFileName());

        $browserFactory = $this->getBrowserFactory(
            $input->getOption('headless')
        );

        $shopFactory = new LocalShopFactory($browserFactory, $systemSettings, $sourceSettings);

        $shop = $shopFactory->makeShop();

        $shop->get('installer')->install();

        return 0;
    }
}
