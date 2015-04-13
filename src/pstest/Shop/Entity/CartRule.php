<?php

namespace PrestaShop\PSTest\Shop\Entity;

class CartRule
{
    private $name;
    private $freeShipping = false;

    const TYPE_NONE     = 0;
    const TYPE_AMOUNT   = 1;
    const TYPE_PERCENT  = 2;

    private $id;
    private $discountType = self::TYPE_PERCENT;
    private $discountAmount = 0;
    private $discountIsBeforeTaxes = true;

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getName()
    {
        return $this->name;
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

    public function setDiscountType($t)
    {
        $this->discountType = $t;
        return $this;
    }

    public function getDiscountType()
    {
        return $this->discountType;
    }

    public function setDiscountAmount($a)
    {
        $this->discountAmount = $a;
        return $this;
    }

    public function getDiscountAmount()
    {
        return $this->discountAmount;
    }

    public function setDiscountIsBeforeTaxes($yes)
    {
        $this->discountIsBeforeTaxes = $yes;
        return $this;
    }

    public function getDiscountIsBeforeTaxes()
    {
        return $this->discountIsBeforeTaxes;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }
}
