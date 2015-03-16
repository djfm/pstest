<?php

namespace PrestaShop\PSTest\Command;

use Symfony\Component\Console\Command\Command;

abstract class BaseCommand extends Command
{
    protected function getConfigurationFileName()
    {
        return 'pstest.settings.json';
    }
}
