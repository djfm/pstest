<?php

namespace PrestaShop\Selenium;

class SeleniumBrowserSettings
{
    private $browserName = 'firefox';
    private $nativeEvents = false;

    public function setBrowserName($name)
    {
        $this->browserName = $name;
        return $this;
    }

    public function setNativeEvents($yes_or_no)
    {
        $this->nativeEvents = $yes_or_no;
        return $this;
    }

    public function getBrowserName()
    {
        return $this->browserName;
    }

    public function getNativeEvents()
    {
        return $this->nativeEvents;
    }

    public function toArray()
    {
        return [
            'browserName' => $this->getBrowserName(),
            'nativeEvents' => $this->getNativeEvents()
        ];
    }
}
