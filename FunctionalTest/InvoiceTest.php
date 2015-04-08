<?php

namespace PrestaShop\FunctionalTest;

use Exception;

use PrestaShop\PSTest\TestCase\TestCase;

use PrestaShop\FunctionalTest\InvoiceTest\Scenario;

class InvoiceTest extends TestCase
{
    private $scenario;
    private $backOffice;

    public function cacheInitialState()
    {
        return [
            'installer' => [
                'install' => [[
                    'language' => 'en',
                    'country' => 'us']
                ]]
        ];
    }

    public function contextProvider()
    {
        $examples = [];
        foreach (scandir(implode(DIRECTORY_SEPARATOR, [__DIR__, 'InvoiceTest', 'examples'])) as $entry) {
            if (preg_match('/\.json$/', $entry)) {
                $examples[] = ['scenario' => basename($entry, '.json')];
            }
        }
        return $examples;
    }

    /**
     * @beforeClass
     */
    public function loadScenario()
    {
        $path = implode(DIRECTORY_SEPARATOR, [__DIR__, 'InvoiceTest', 'examples', $this->context('scenario') . '.json']);
        $this->scenario = new Scenario;
        $this->scenario->loadFromJSONFile($path);
    }

    /**
     * @beforeClass
     */
    public function loginToTheBackOffice()
    {
        $this->info('Logging to the Back-Office');
        $this->backOffice = $this->shop->get('back-office')->login();
    }

    /**
     * @beforeClass
     */
    public function createNeededTaxes()
    {
        $this->info('Creating the taxes I need');
        foreach ($this->scenario->getTaxRulesGroups() as $trg) {
            $this->backOffice->get('taxes')->createTaxRulesGroup($trg);
        }
    }

    /**
     * @beforeClass
     */
    public function setupRoundingOptions()
    {
        $this->info('Setting the rounding options');
        $this->backOffice->get('settings')
             ->setRoundingType($this->scenario->getRoundingType())
             ->setRoundingMode($this->scenario->getRoundingMode())
        ;
    }

    /**
     * @beforeClass
     */
    public function createTestCarrier()
    {
        $this->info('Creating the carrier I need');
        $this->backOffice->get('carriers')->createCarrier($this->scenario->getCarrier());
    }

    /**
     * @beforeClass
     */
    public function createNeededProducts()
    {
        $this->info('Creating the products I need');
        foreach ($this->scenario->getProducts() as $product) {
            $this->backOffice->get('products')->createProduct($product);
        }
    }

    public function test_customer_can_log_in()
    {
        $this->shop->get('front-office')->visitMyAccount();
    }
}
