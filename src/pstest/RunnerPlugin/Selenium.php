<?php

namespace PrestaShop\PSTest\RunnerPlugin;

use PrestaShop\Selenium\SeleniumServerFactory;
use PrestaShop\Selenium\SeleniumBrowserFactory;
use PrestaShop\Selenium\SeleniumBrowserSettings;
use PrestaShop\Selenium\Xvfb\XvfbServerFactory;

use PrestaShop\TestRunner\RunnerPlugin;
use PrestaShop\TestRunner\Command\TestRun as TestRunCommand;
use Symfony\Component\Console\Input\InputOption;

use PrestaShop\TestRunner\Command\CLIOption;

class Selenium extends RunnerPlugin
{
    private $server;
    private $xvfbServer;
    private $recordScreenshots = false;

    public function getCLIOptions()
    {
        return [
            new CLIOption('headless', null, InputOption::VALUE_NONE, 'Run the tests headlessly if Xvfb is available'),
            new CLIOption('record-screenshots', 's', InputOption::VALUE_NONE, 'Record screenshots during tests execution')
        ];
    }

    public function setup(array $options = array())
    {
        $headless = !empty($options['headless']);
        $this->recordScreenshots = !empty($options['record-screenshots']);

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
        return [
            'serverSettings' => $this->server->getSettings(),
            'recordScreenshots' => $this->recordScreenshots
        ];
    }
}
