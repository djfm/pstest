<?php

namespace PrestaShop\PrestaShop\FunctionalTest;

use Exception;

use PrestaShop\PSTest\TestCase\PrestaShopTest;

use PrestaShop\PrestaShop\FunctionalTest\InvoiceTest\Scenario;

class InvoiceTest extends PrestaShopTest
{
    private $scenario;
    private $backOffice;
    private $orderId;
    private $invoiceData;

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
        $this->info('Logging in to the Back-Office');
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

    /**
     * @beforeClass
     */
    public function createNeededCartRules()
    {
        $cartRules = $this->scenario->getCartRules();

        if (empty($cartRules)) {
            $this->info('No Cart Rules necessary');
        } else {
            $this->info('Creating the Cart Rules I need');
            foreach ($cartRules as $cartRule) {
                $this->shop->get('back-office')->get('cart-rules')->createCartRule($cartRule);
            }
        }
    }

    public function test_order_is_made()
    {
        $this->shop->get('front-office')->login();

        foreach ($this->scenario->getProducts() as $product) {
            $this->shop->get('front-office')
                 ->visitProduct($product)
                 ->setQuantity($product->getQuantity())
                 ->addToCart()
            ;
        }

        $this->orderId = $this->shop->get('front-office')->checkoutCart([
            'carrierName' => $this->scenario->getCarrier()->getName()
        ]);
    }

    public function test_order_is_validated_in_the_backoffice()
    {
        $orderPage = $this->backOffice
                          ->get('orders')
                          ->visitById($this->orderId)
                          ->validate();

        $this->invoiceData = $orderPage->getInvoiceData();

        $this->addFileArtefact('invoice.json', [], json_encode($this->invoiceData, JSON_PRETTY_PRINT));
        $this->addFileArtefact('invoice.pdf', [], $orderPage->getInvoicePDF());
    }

    public function test_invoice_has_expected_values()
    {
        $this->scenario->checkInvoiceData($this->invoiceData);
    }

    // @todo
    public function xtest_invoice_coherence()
    {

    }

    // @todo
    public function xtest_invoice_stable_if_rounding_settings_changed()
    {

    }
}
