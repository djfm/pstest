<?php

namespace PrestaShop\PSTest\Shop\Entity;

class TaxRule
{
    const THIS_TAX_ONLY = 0;
    const COMBINE = 1;
    const ONE_AFTER_ANOTHER = 2;

    private $id;
    private $tax;
    private $country = null;
    private $zipCodeRange = 0;
    private $behavior = self::THIS_TAX_ONLY;
    private $description = 'A Tax Rule';

    public function setTax(Tax $tax)
    {
        $this->tax = $tax;
        return $this;
    }

    public function getTax()
    {
        return $this->tax;
    }

    public function getZipCodeRange()
    {
        return $this->zipCodeRange;
    }

    public function setZipCodeRange($range)
    {
        $this->zipCodeRange = $range;
        return $this;
    }

    public function setBehavior($behavior)
    {
        $this->behavior = $behavior;
        return $this;
    }

    public function getBehavior()
    {
        return $this->behavior;
    }

    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getCountryId()
    {
        return $this->countryId;
    }

    public function setCountryId($id)
    {
        $this->countryId = $id;
        return $this;
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

    public function setCountry(Country $country)
    {
        $this->country = $country;
        return $this;
    }

    public function getCountry()
    {
        return $this->country;
    }
}
