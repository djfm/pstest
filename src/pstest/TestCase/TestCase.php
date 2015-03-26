<?php

namespace PrestaShop\PSTest\TestCase;

use PrestaShop\TestRunner\TestCase\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    public function getRunnerPlugins()
    {
        /**
         * @todo: why not just return the instance of the plugin directly?
         */
        return [
            ['PrestaShop\\PSTest\\RunnerPlugin\\Selenium']
        ];
    }

    /**
     * @beforeClass
     */
    public function prepareSelenium()
    {
        //seleniumServerSettings
        var_dump(getenv('seleniumServerSettings'));
        var_dump($_SERVER);
    }

    /**
     * @beforeClass
     */
    public function setShop()
    {
    }
}
