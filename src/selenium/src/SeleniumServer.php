<?php

namespace PrestaShop\Selenium;

use PrestaShop\Proc\Proc;

class SeleniumServer
{
    private $settings;
    private $proc;

    public function __construct(SeleniumServerSettings $settings, Proc $proc)
    {
        $this->settings = $settings;
        $this->proc = $proc;
    }

    public function serverResponds($maxAttempts = 5)
    {
        $interval = 1;

        for ($i = 0; $i < $maxAttempts; $i++) {
            $ch = curl_init(rtrim($this->settings->getURL(), '/') . '/status');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            $status = json_decode($response, true);
            curl_close($ch);
            if(json_last_error() === JSON_ERROR_NONE && isset($status['status']) && $status['status'] === 0) {
                return true;
            }
            sleep($interval);
        }

        return false;
    }

    public function isRunning()
    {
        if (!$this->proc->isRunning()) {
            return false;
        }

        return $this->serverResponds();
    }

    public function shutDown()
    {
        $killChildren = true;
        return $this->proc->terminate($killChildren);
    }

    public function getSettings()
    {
        return $this->settings;
    }
}
