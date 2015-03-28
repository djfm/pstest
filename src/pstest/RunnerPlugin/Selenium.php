<?php

namespace PrestaShop\PSTest\RunnerPlugin;

use PrestaShop\Selenium\SeleniumServerFactory;
use PrestaShop\Selenium\SeleniumBrowserFactory;
use PrestaShop\Selenium\SeleniumBrowserSettings;
use PrestaShop\Selenium\Xvfb\XvfbServerFactory;

use PrestaShop\TestRunner\RunnerPlugin;
use PrestaShop\TestRunner\Command\TestRun as TestRunCommand;
use Symfony\Component\Console\Input\InputOption;

class Selenium extends RunnerPlugin
{
    private $server;
    private $xvfbServer;

    public function setup(array $options = array())
    {
        $headless = !empty($options['headless']);

        $ssf = new SeleniumServerFactory;

        $this->xvfbServer = null;

        $xsf = new XvfbServerFactory;
		if ($headless && $xsf->hasXvfbProgram()) {
			$this->xvfbServer = $xsf->makeServer();
			$ssf->setXvfb($this->xvfbServer);
		}

        $this->server = $ssf->makeServer();
    }

    public function teardown()
    {
        $this->server->shutDown();

        if ($this->xvfbServer) {
            $this->xvfbServer->shutDown();
        }
    }

    public function getRunnerPluginData()
    {
        return $this->server->getSettings();
    }

    public function addOptionsToCommand(TestRunCommand $command)
    {
        $command->addOption('headless', null, InputOption::VALUE_NONE, 'Run the tests headlessly if Xvfb is available.');

        return ['headless'];
    }
}
