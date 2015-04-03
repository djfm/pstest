<?php

namespace PrestaShop\IoC\Tests\Fixtures;

class CycleB
{
    public function __construct(CycleA $a)
    {
    }
}
