<?php

namespace PrestaShop\Selenium\Xvfb;

use PrestaShop\Proc\Proc;

class XvfbServer
{
    private $proc;
    private $displayNumber;

    public function __construct(Proc $proc, $displayNumber)
    {
        $this->proc = $proc;
        $this->displayNumber = $displayNumber;
    }

    public function isRunning()
    {
        return $this->proc->isRunning();
    }

    public function shutDown()
    {
        $killChildren = true;
        return $this->proc->terminate($killChildren);
    }

    public function getDisplayNumber()
    {
        return $this->displayNumber;
    }

    public function getDisplayName()
    {
        return ':' . $this->getDisplayNumber();
    }
}
