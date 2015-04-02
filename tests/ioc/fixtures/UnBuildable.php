<?php

namespace PrestaShop\IoC\Tests\Fixtures;

class UnBuildable
{
    private $dummy;
    private $something;
    private $cannotbuild;

    public function __construct(Dummy $dummy, $cannotbuild, $something = 4)
    {
        $this->dummy = $dummy;
        $this->cannotbuild = $cannotbuild;
        $this->something = $something;
    }
}
