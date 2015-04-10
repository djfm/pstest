<?php

namespace PrestaShop\PrestaShop\FunctionalTest\InvoiceTest;

use Exception;
use PHPUnit_Framework_Assert as Assert;

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

    private $totalPriceExpected = [];
    private $taxBreakdownsExpected = [];

    public function __construct()
    {
        $this->carrier = new Carrier;
        $uid = 'SeleniumCarrier_' . md5(microtime(true));
        $this->carrier->setName($uid)->setTransitTime($uid)->setFreeShipping(true);
    }

    private function getTaxRulesGroupFromRate($rate)
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

    private function loadSettings(array $scenario)
    {
        if (isset($scenario['settings']['rounding_type'])) {
            $this->rounding_type = $scenario['settings']['rounding_type'];
        }

        if (isset($scenario['settings']['rounding_mode'])) {
            $this->rounding_mode = $scenario['settings']['rounding_mode'];
        }

        return $this;
    }

    private function loadCarrier(array $scenario)
    {
        if (isset($scenario['shipping']['price']) && $scenario['shipping']['price'] > 0) {
            $carrierPrice = $scenario['shipping']['price'];
            $range = new CarrierRange;
            $range->setFromIncluded(0)->setToExcluded(1000);
            $range->setCost($carrierPrice);
            $this->carrier->addRange($range)->setOutOfRangeBehavior(Carrier::BEHAVIOR_HIGHEST);

            if (isset($scenario['shipping']['tax_rate']) && $scenario['shipping']['tax_rate'] > 0) {
                $rate = $scenario['shipping']['tax_rate'];
                $this->carrier->setTaxRulesGroup($this->getTaxRulesGroupFromRate($rate));
            }
        }

        return $this;
    }

    private function loadProducts(array $scenario)
    {
        if (!isset($scenario['products'])) {
            throw new Exception('Missing products definition!');
        }

        foreach ($scenario['products'] as $name => $data) {
            $product = new Product;
            $product
                ->setName($name)
                ->setPrice($data['price'])
                ->setQuantity($data['quantity'])
            ;

            if (isset($data['tax_rate'])) {
                $product->setTaxRulesGroup($this->getTaxRulesGroupFromRate($data['tax_rate']));
            }

            $this->products[] = $product;
        }
    }

    private function loadExpectations(array $scenario)
    {
        if (!$scenario['expect']) {
            throw new Exception('No assertions found! Missing an `expect` key in scenario.');
        }

        foreach ($scenario['expect'] as $what => $how) {
            if (is_scalar($how)) {
                $this->assertsTotalPrice($what, $how);
            } else if ('taxes' === $what){
                foreach ($how as $type => $amountsByRate) {
                    $this->assertsTaxBreakdown($type, $amountsByRate);
                }
            }
        }
    }

    public function loadFromJSONFile($path)
    {
        if (!file_exists($path)) {
            throw new Exception(sprintf('File `%s` does not exist.', $path));
        }

        $scenario = @json_decode(file_get_contents($path), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception(sprintf('Invalid JSON found in file `%s`.', $path));
        }

        $this->loadSettings($scenario);
        $this->loadCarrier($scenario);
        $this->loadProducts($scenario);
        $this->loadExpectations($scenario);
    }

    public function normalizePriceExpectationFormulation($formulation)
    {
        $formulation = trim($formulation);

        $normalized = explode(' ', $formulation)[0];

        $beforeDiscounts = !preg_match('/\bafter\s+(?:discount|discounts)\b/', $formulation);
        $beforeTaxes = !preg_match('/\bafter\s+(?:tax|taxes)\b/', $formulation);

        if ($beforeDiscounts) {
            $normalized .= '_before_discounts';
        } else {
            $normalized .= '_after_discounts';
        }

        if ($beforeTaxes) {
            $normalized .= '_before_taxes';
        } else {
            $normalized .= '_after_taxes';
        }

        if (null === $this->mapTotalPriceKey($normalized)) {
            throw new Exception(sprintf('Cannot make an assertion about unknown price `%s`.', $normalized));
        }

        return $normalized;
    }

    public function assertsTotalPrice($field, $expectedValue)
    {
        $this->totalPriceExpected[$this->normalizePriceExpectationFormulation($field)] = $expectedValue;

        return $this;
    }

    public function assertsTaxBreakdown($type, array $amountsByRate)
    {
        if ($type !== 'products') {
            throw new Exception(sprintf('Don\'t know how to assert `%s` tax breakdown.', $type));
        }
        $this->taxBreakdownsExpected[$type] = $amountsByRate;

        return $this;
    }

    private function mapTotalPriceKey($normalizedKey)
    {
        $mapping = [
            'products_before_discounts_before_taxes' => 'products_before_discounts_tax_excl',
            'products_before_discounts_after_taxes' => 'products_before_discounts_tax_incl',
            'products_after_discounts_before_taxes' => 'products_after_discounts_tax_excl',
            'products_after_discounts_after_taxes' => 'products_after_discounts_tax_incl',
            'shipping_before_discounts_before_taxes' => 'shipping_tax_excl',
            'shipping_before_discounts_after_taxes' => 'shipping_tax_incl',
            'wrapping_before_discounts_before_taxes' => 'shipping_tax_excl',
            'wrapping_before_discounts_after_taxes' => 'wrapping_tax_incl',
            'total_after_discounts_before_taxes' => 'total_paid_tax_excl',
            'total_after_discounts_after_taxes' => 'total_paid_tax_incl',
        ];

        if (!array_key_exists($normalizedKey, $mapping)) {
            return null;
        }

        return $mapping[$normalizedKey];
    }

    private function runTotalPriceAssertions(array $footer)
    {
        foreach ($this->totalPriceExpected as $key => $expected) {
            $nativeKey = $this->mapTotalPriceKey($key);
            if (!array_key_exists($nativeKey, $footer)) {
                throw new Exception(sprintf('Missing `%s` field in invoice footer.', $nativeKey));
            }
            Assert::assertEquals($expected, $footer[$nativeKey], sprintf('Invalid price for `%s`.', $key));
        }
    }

    public function runTaxBreakdownsAssertions(array $taxTab)
    {
        $floatToStr = function ($float) {
            return sprintf('%.10f', $float);
        };

        foreach ($this->taxBreakdownsExpected as $type => $expectedAmounts) {
            if ('products' === $type) {

                $actualAmounts = [];
                foreach ($taxTab['product_tax_breakdown'] as $rate => $amounts) {
                    $actualAmounts[$floatToStr($rate)] = (float)$amounts['total_amount'];
                }

                foreach ($expectedAmounts as $rate => $expected) {
                    $rate = $floatToStr($rate);
                    if (!array_key_exists($rate, $actualAmounts)) {
                        throw new Exception(
                            sprintf(
                                'There is no tax amount for rate `%1$s` in the `%2$s` breakdown.',
                                $rate, $type
                            )
                        );
                    }
                    Assert::assertEquals(
                        $expected,
                        $actualAmounts[$rate],
                        sprintf(
                            'Incorrect tax amount for rate `%1$s` in the `%2$s` breakdown.',
                            $rate, $type
                        )
                    );
                }
            }
        }
    }

    public function checkInvoiceData(array $invoiceData)
    {
        if (!isset($invoiceData['footer'])) {
            throw new Exception('Missing footer in invoice data.');
        }

        $this->runTotalPriceAssertions($invoiceData['footer']);
        $this->runTaxBreakdownsAssertions($invoiceData['tax_tab']);

        return $this;
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

    public function getTaxRulesGroups()
    {
        return $this->taxRulesGroups;
    }

    public function getCarrier()
    {
        return $this->carrier;
    }
}
