<?php

namespace PrestaShop\PSTest\Shop\Service\BackOffice;

use Exception;

use PrestaShop\PSTest\Shop\BackOfficeService;
use PrestaShop\PSTest\Shop\Entity\Carrier;
use PrestaShop\PSTest\Shop\Shop;
use PrestaShop\PSTest\Shop\PageObject\BackOffice\CarrierWizard\GeneralSettingsPage;

class Carriers extends BackOfficeService
{
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
        } else {
            $costsSettings->setTaxRulesGroupId(0);
        }

        $costsSettings->setOutOfRangeBehavior($carrier->getOutOfRangeBehavior());

        $costsSettings->setRanges($carrier->getRanges());

        $sizeWeightEtc = $costsSettings->nextStep();
        $sizeWeightEtc
            ->setMaximumPackageWidth($carrier->getMaximumPackageWidth())
            ->setMaximumPackageHeight($carrier->getMaximumPackageHeight())
            ->setMaximumPackageDepth($carrier->getMaximumPackageDepth())
            ->setMaximumPackageWeight($carrier->getMaximumPackageWeight())
            ->setGroupAccess($carrier->getGroupAccess())
        ;

        $finalPage = $sizeWeightEtc->nextStep();
        $finalPage->setEnabled($carrier->getEnabled())->submit();

        $this->browser
             ->fillIn('input[name="carrierFilter_name"]', $carrier->getName())
             ->click('#submitFilterButtoncarrier')
        ;

        $id = (int)$this->browser->getText('#table-carrier tr:first-of-type td:nth-of-type(2)');

        $carrier->setId($id);

        $this->browser->clickButtonNamed('submitResetcarrier');

        return $this;
    }
}
