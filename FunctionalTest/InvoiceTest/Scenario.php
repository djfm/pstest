<?php

namespace PrestaShop\FunctionalTest\InvoiceTest;

use Exception;

use PrestaShop\PSTest\Shop\Entity\CarrierRange;
use PrestaShop\PSTest\Shop\Entity\Carrier;
use PrestaShop\PSTest\Shop\Entity\Tax;
use PrestaShop\PSTest\Shop\Entity\TaxRule;
use PrestaShop\PSTest\Shop\Entity\TaxRulesGroup;
use PrestaShop\PSTest\Shop\Entity\Product;

class Scenario
{
    private $rounding_type = 'line';
    private $rounding_mode = 'half_up';
    private $carrier;
    private $taxRulesGroups = [];
    private $products = [];

    public function __construct()
    {
        $this->carrier = new Carrier;
        $uid = 'SeleniumCarrier_' . md5(microtime(true));
        $this->carrier->setName($uid)->setTransitTime($uid)->setFreeShipping(true);
    }

    public function getCarrier()
    {
        return $this->carrier;
    }

    public function getTaxRulesGroupFromRate($rate)
    {
        $key = "$rate% TRG";
        if (!isset($this->taxRulesGroups[$key])) {
            $taxRulesGroup = new TaxRulesGroup;

            $tax = (new Tax)->setName("$rate% Tax")->setRate($rate);
            $taxRule = (new TaxRule)->setTax($tax);
            $taxRulesGroup->setName($key)->addTaxRule($taxRule);

            $this->taxRulesGroups[$key] = $taxRulesGroup;
        }

        return $this->taxRulesGroups[$key];
    }

    public function getTaxRulesGroups()
    {
        return $this->taxRulesGroups;
    }

    public function loadFromJSONFile($path)
    {
        if (!file_exists($path)) {
            throw new Exeption(sprintf('File `%s` does not exist.', $path));
        }

        $json = @json_decode(file_get_contents($path), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exeption(sprintf('Invalid JSON found in file `%s`.', $path));
        }

        if (isset($json['meta']['rounding_type'])) {
            $this->rounding_type = $json['meta']['rounding_type'];
        }

        if (isset($json['meta']['rounding_mode'])) {
            $this->rounding_mode = $json['meta']['rounding_mode'];
        }

        if (isset($json['shipping']['price']) && $json['shipping']['price'] > 0) {
            $carrierPrice = $json['shipping']['price'];
            $range = new CarrierRange;
            $range->setFromIncluded(0)->setToExcluded(1000);
            $range->setCost($carrierPrice);
            $this->carrier->addRange($range)->setOutOfRangeBehavior(Carrier::BEHAVIOR_HIGHEST);

            if (isset($json['shipping']['taxRate']) && $json['shipping']['taxRate'] > 0) {
                $rate = $json['shipping']['taxRate'];
                $this->carrier->setTaxRulesGroup($this->getTaxRulesGroupFromRate($rate));
            }
        }

        if (!isset($json['products'])) {
            throw new Exception('Missing products definition!');
        }

        foreach ($json['products'] as $name => $data) {
            $product = new Product;
            $product
                ->setName($name)
                ->setPrice($data['price'])
                ->setQuantity($data['quantity'])
            ;

            if (isset($data['taxRate'])) {
                $product->setTaxRulesGroup($this->getTaxRulesGroupFromRate($data['taxRate']));
            }

            $this->products[] = $product;
        }
    }

    public function getRoundingType()
    {
        return $this->rounding_type;
    }

    public function getRoundingMode()
    {
        return $this->rounding_mode;
    }

    public function getProducts()
    {
        return $this->products;
    }
}
