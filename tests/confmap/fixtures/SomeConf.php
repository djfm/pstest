<?php

namespace PrestaShop\ConfMap\Tests\Fixtures;

use PrestaShop\ConfMap\Configuration;

/**
 * @root some_conf
 */
class SomeConf extends Configuration
{
    /**
     * @conf
     */
    private $a;

    /**
     * @conf
     */
    private $b;

    /**
     * @conf database.user
     */
    private $database_user;

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

    public function getDatabaseUser()
    {
        return $this->database_user;
    }
}
