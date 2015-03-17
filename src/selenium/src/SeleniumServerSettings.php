<?php

namespace PrestaShop\Selenium;

use PrestaShop\ConfMap\Configuration;

/**
 * @root selenium
 */
class SeleniumServerSettings extends Configuration
{
    /**
     * @conf
     */
    private $url;

    public function setURL($url)
    {
        $this->url = $url;

        return $this;
    }

    public function getURL()
    {
        return $this->url;
    }
}
