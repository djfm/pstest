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
    private $a = 'default_a';

    /**
     * @conf
     */
    private $b = 'default_b';

    /**
     * @conf database.user
     */
    public $database_user;

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
