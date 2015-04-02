<?php

namespace PrestaShop\IoC\Tests\Fixtures;

class ClassWithDepAndDefault
{
    private $dummy;
    private $something;

    public function __construct(Dummy $dummy, $something = 4)
    {
        $this->dummy = $dummy;
        $this->something = $something;
    }
}
