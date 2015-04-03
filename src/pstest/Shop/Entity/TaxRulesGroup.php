<?php

namespace PrestaShop\PSTest\Shop\Entity;

class TaxRulesGroup
{
    private $name;
    private $enabled;
    private $taxRules = [];
    private $id;

    public function setEnabled($yes = true)
    {
        $this->enabled = $yes;
        return $this;
    }

    public function getEnabled()
    {
        return $this->enabled;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function addTaxRule(TaxRule $taxRule)
    {
        $this->taxRules[] = $taxRule;
        return $this;
    }

    public function getTaxRules()
    {
        return $this->taxRules;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
}
