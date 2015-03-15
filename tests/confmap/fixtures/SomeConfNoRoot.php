<?php

namespace PrestaShop\ConfMap\Tests\Fixtures;

use PrestaShop\ConfMap\Configuration;

class SomeConfNoRoot extends Configuration
{
    /**
     * @conf
     */
    private $a;

    /**
     * @conf
     */
    private $b;

    public function getA()
    {
        return $this->a;
    }

    public function setA($a)
    {
        $this->a = $a;
        return $this;
    }

    public function getB()
    {
        return $this->b;
    }

    public function setB($b)
    {
        $this->b = $b;
        return $this;
    }
}
