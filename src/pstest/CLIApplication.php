<?php

namespace PrestaShop\PSTest;

use Symfony\Component\Console\Application;

use PrestaShop\PSTest\Command\SetupCheck;
use PrestaShop\PSTest\Command\ConfigCreate;
use PrestaShop\PSTest\Command\ShopInstall;
use PrestaShop\PSTest\Command\TestRun;

class CLIApplication extends Application
{
    public function __construct()
    {
        parent::__construct();
        $this->addCustomCommands();
    }

    private function addCustomCommands()
    {
        $this->add(new SetupCheck);
        $this->add(new ConfigCreate);
        $this->add(new ShopInstall);
        $this->add(new TestRun);
    }
}
