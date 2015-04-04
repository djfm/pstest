<?php

namespace PrestaShop\PSTest\Shop\Entity;

class Carrier
{
    const BEHAVIOR_HIGHEST = 0;
    const BEHAVIOR_DISABLE = 1;

    private $id;
    private $name;
    private $transitTime;
    private $speedGrade;
    private $addHandlingCosts;
    private $freeShipping;
    private $billAccordingToPrice;
    private $billAccordingToWeight;
    private $taxRulesGroup;
    private $outOfRangeBehavior = self::BEHAVIOR_HIGHEST;
    private $ranges = [];
    private $maximumPackageWidth;
    private $maximumPackageHeight;
    private $maximumPackageDepth;
    private $maximumPackageWeight;
    private $groupAccess;

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setTransitTime($time)
    {
        $this->transitTime = $time;
        return $this;
    }

    public function getTransitTime()
    {
        return $this->transitTime;{
    }
}
    public function setSpeedGrade($grade)
    {
        $this->speedGrade = $grade;
        return $this;
    }

    public function getSpeedGrade()
    {
        return $this->speedGrade;
    }

    public function setAddHandlingCosts($yes = true)
    {
        $this->addHandlingCosts = $yes;
        return $this;
    }

    public function getAddHandlingCosts()
    {
        return $this->addHandlingCosts;
    }

    public function setFreeShipping($yes = true)
    {
        $this->freeShipping = $yes;
        return $this;
    }

    public function getFreeShipping()
    {
        return $this->freeShipping;
    }

    public function setBillAccordingToPrice($yes = true)
    {
        $this->billAccordingToPrice = $yes;
        $this->billAccordingToWeight = !$yes;

        return $this;
    }

    public function getBillAccordingToPrice()
    {
        return $this->billAccordingToPrice;
    }

    public function setBillAccordingToWeight($yes = true)
    {
        $this->billAccordingToPrice = !$yes;
        $this->billAccordingToWeight = $yes;

        return $this;
    }

    public function getBillAccordingToWeight()
    {
        return $this->billAccordingToWeight;
    }

    public function setTaxRulesGroup(TaxRulesGroup $taxRulesGroup)
    {
        $this->taxRulesGroup = $taxRulesGroup;
        return $this;
    }

    public function getTaxRulesGroup()
    {
        return $this->taxRulesGroup;
    }

    public function setOutOfRangeBehavior($behavior)
    {
        $this->outOfRangeBehavior = $behavior;
        return $this;
    }

    public function getOutOfRangeBehavior()
    {
        return $this->outOfRangeBehavior;
    }

    public function addRange(CarrierRange $range)
    {
        $this->ranges[] = $range;
        return $this;
    }

    public function getRanges()
    {
        return $this->ranges;
    }
}
