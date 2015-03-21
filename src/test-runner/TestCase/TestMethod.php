<?php

namespace PrestaShop\TestRunner\TestCase;

class TestMethod
{
    private $name;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function run($instance)
    {
        $arguments = [];
        call_user_func_array([$instance, $this->getName()], $arguments);
    }
}
