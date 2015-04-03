<?php

namespace PrestaShop\PSTest\Shop\Service\BackOffice;

use Exception;

use PrestaShop\PSTest\Shop\Entity\Carrier;

use PrestaShop\PSTest\Shop\Shop;

use PrestaShop\PSTest\Shop\PageObject\BackOffice\CarrierWizard\GeneralSettingsPage;

class Carriers
{
    private $shop;
    private $backOffice;
    private $browser;

    public function __construct(Shop $shop)
    {
        $this->shop = $shop;
        $this->backOffice = $this->shop->get('back-office');
        $this->browser = $shop->getBrowser();
    }

    public function createCarrier(Carrier $carrier)
    {
        $this->backOffice->visitController('AdminCarriers');
        $this->browser->click('#page-header-desc-carrier-new_carrier')->click('#configuration_form a.btn');

        $generalSettings = new GeneralSettingsPage($this->shop);

        $generalSettings
        ->setName($carrier->getName())
        ->setTransitTime($carrier->getTransitTime())
        ->setSpeedGrade($carrier->getSpeedGrade())
        ;

        $costsSettings = $generalSettings->nextStep();
        $costsSettings
        ->setAddHandlingCosts($carrier->getAddHandlingCosts())
        ->setFreeShipping($carrier->getFreeShipping())
        ->setBillAccordingToWeight($carrier->getBillAccordingToWeight())
        ;

        if ($carrier->getTaxRulesGroup()) {
            $trgId = $carrier->getTaxRulesGroup()->getId();
            if (!$trgId) {
                throw new Exception('Specified Carrier TaxRulesGroup is missing an ID.');
            }

            $costsSettings->setTaxRulesGroupId($trgId);
        }

        $costsSettings->setOutOfRangeBehavior($carrier->getOutOfRangeBehavior());
    }
}
