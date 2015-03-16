<?php

namespace PrestaShop\PSTest;

use Symfony\Component\Console\Application;

use PrestaShop\PSTest\Command\ConfigCreate;
use PrestaShop\PSTest\Command\ShopInstall;

class CLIApplication extends Application
{
    public function __construct()
    {
        parent::__construct();
        $this->addCustomCommands();
    }

    private function addCustomCommands()
    {
        $this->add(new ConfigCreate);
        $this->add(new ShopInstall);
    }
}
