<?php

namespace PrestaShop\IoC\Tests\Fixtures;

class DepBuiltByClosure
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }
}
