<?php

namespace PrestaShop\IoC\Tests\Fixtures;

class ClassWithDep
{
    private $dummy;
    
    public function __construct(Dummy $dummy)
    {
        $this->dummy = $dummy;
    }
}
