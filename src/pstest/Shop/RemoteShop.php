<?php

namespace PrestaShop\PSTest\Shop;

use Exception;

class RemoteShop extends Shop
{
    private $browser;
    private $frontOfficeURL;
    private $backOfficeURL;

    public function setBrowser($browser)
    {
        $this->browser = $browser;
        return $this;
    }

    public function getBrowser()
    {
        return $this->browser;
    }

    public function getFrontOfficeURL()
    {
        return $this->frontOfficeURL;
    }

    public function setFrontOfficeURL($frontOfficeURL)
    {
        $this->frontOfficeURL = $frontOfficeURL;
        return $this;
    }

    public function getBackOfficeURL()
    {
        return $this->backOfficeURL;
    }

    public function setBackOfficeURL($backOfficeURL)
    {
        $this->backOfficeURL = $backOfficeURL;
        return $this;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }
}
