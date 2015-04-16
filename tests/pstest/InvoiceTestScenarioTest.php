<?php

namespace PrestaShop\PSTest\Tests;

use PHPUnit_Framework_TestCase;

use PrestaShop\PrestaShop\FunctionalTest\InvoiceTest\Scenario;

class InvoiceTestScenarioTest extends PHPUnit_Framework_TestCase
{
    public function formulationExamples()
    {
        return [
            ['products_before_discounts_before_taxes', 'products'],
            ['products_before_discounts_after_taxes', 'products after taxes'],
            ['shipping_before_discounts_after_taxes', ' shipping after taxes before discounts ']
        ];
    }

    /**
     * @dataProvider formulationExamples
     */
    public function test_normalizePriceExpectationFormulation($expected, $input)
    {
        $scenario = new Scenario;
        $this->assertEquals($expected, $scenario->normalizePriceExpectationFormulation($input));
    }

    public function test_invoice_total_price_assertions_ok()
    {
        $invoiceData = json_decode(
            file_get_contents(
                implode(DIRECTORY_SEPARATOR, [__DIR__, 'fixtures', 'InvoiceTestScenarioTest', 'a.json'])
            ),
            true
        );

        $scenario = new Scenario;
        $scenario->assertsTotalPrice('products', 26);
        $scenario->assertsTotalPrice('products before tax', 26);
        $scenario->assertsTotalPrice('products before taxes', 26);
        $scenario->assertsTotalPrice('shipping after taxes', 42);
        $scenario->assertsTotalPrice('products before taxes and discount', 26);

        $scenario->checkInvoiceData($invoiceData);
    }

    /**
     * @expectedException PHPUnit_Framework_AssertionFailedError
     */
    public function test_invoice_total_price_assertions_ko()
    {
        $invoiceData = json_decode(
            file_get_contents(
                implode(DIRECTORY_SEPARATOR, [__DIR__, 'fixtures', 'InvoiceTestScenarioTest', 'a.json'])
            ),
            true
        );

        $scenario = new Scenario;
        $scenario->assertsTotalPrice('products', 27);

        $scenario->checkInvoiceData($invoiceData);
    }
}
