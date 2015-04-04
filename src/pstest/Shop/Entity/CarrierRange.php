<?php

namespace PrestaShop\PSTest\Shop\Entity;

class CarrierRange
{
    private $fromIncluded;
    private $toExcluded;
    private $cost;

    private $zones = [];

    public function setFromIncluded($fromIncluded)
    {
        $this->fromIncluded = $fromIncluded;
        return $this;
    }

    public function getFromIncluded()
    {
        return $this->fromIncluded;
    }

    public function setToExcluded($toExcluded)
    {
        $this->toExcluded = $toExcluded;
        return $this;
    }

    public function getToExcluded()
    {
        return $this->toExcluded;
    }

    public function setCost($cost)
    {
        $this->cost = $cost;
        return $this;
    }

    public function getCost()
    {
        return $this->cost;
    }

    public function addZone(CarrierZone $zone)
    {
        $this->zones[] = $zone;
        return $this;
    }

    public function getZones()
    {
        return $this->zones;
    }
}
