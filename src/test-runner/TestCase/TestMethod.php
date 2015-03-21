<?php

namespace PrestaShop\TestRunner\TestCase;

use Closure;
use Exception;

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
        $name = $this->getName();

        $callable = function () use ($name, $arguments) {
            call_user_func_array([$this, $name], $arguments);
        };

        $callable = Closure::bind($callable, $instance, $instance);

        try {
            $callable();
            return 'ok';
        } catch (Exception $e) {
            return 'ko';
        }
    }
}
