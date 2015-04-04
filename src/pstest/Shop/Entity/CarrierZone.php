<?php

namespace PrestaShop\PSTest\Shop\Entity;

class CarrierZone extends Zone
{
    private $cost;

    public function setCost($cost)
    {
        $this->cost = $cost;
        return $this;
    }

    public function getCost()
    {
        return $this->cost;
    }
}
