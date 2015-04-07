<?php

namespace PrestaShop\PSTest\Shop\Entity;

class Product
{
    const TYPE_STANDARD_PRODUCT = 0;
    const TYPE_PACK_OF_PRODUCTS = 1;
    const TYPE_VIRTUAL_PRODUCT  = 2;

    const OUT_OF_STOCK_DENY_ORDERS  = 1;
    const OUT_OF_STOCK_ALLOW_ORDERS = 2;
    const OUT_OF_STOCK_SHOP_DEFAULT = 3;

    private $name;
    private $price;
    private $taxRulesGroup;
    private $quantity = 0;

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setTaxRulesGroup(TaxRulesGroup $trg)
    {
        $this->taxRulesGroup = $trg;
        return $this;
    }

    public function getTaxRulesGroup()
    {
        return $this->taxRulesGroup;
    }

    public function setQuantity($q)
    {
        $this->quantity = $q;
        return $this;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }
}
