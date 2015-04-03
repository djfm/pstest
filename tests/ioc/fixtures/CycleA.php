<?php

namespace PrestaShop\IoC\Tests\Fixtures;

class CycleA
{
    public function __construct(CycleB $b)
    {
    }
}
