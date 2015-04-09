<?php

namespace PrestaShop\PSTest\Shop\Service\BackOffice;

use Exception;

use PrestaShop\PSTest\Shop\BackOfficeService;

class Settings extends BackOfficeService
{
    public function getKnownRoundingModes()
    {
        return [
            'up' => 0,
            'down' => 1,
            'half_up' => 2,
            'half_down' => 3,
            'half_even' => 4,
            'half_odd' => 5
        ];
    }
    public function getKnownRoundingTypes()
    {
        return [
            'item' => 1,
            'line' => 2,
            'total' => 3
        ];
    }

    public function setRoundingType($type)
    {
        $this->backOffice->visitController('AdminPreferences');
        $prefs = $this->get('PageObject:BackOffice\AdminPreferencesPage');

        if (!array_key_exists($type, $this->getKnownRoundingTypes())) {
            throw new Exception(sprintf('Unknown rounding type `%s`.', $type));
        }

        $typeId = $this->getKnownRoundingTypes()[$type];

        if ($typeId != $prefs->setRoundingType($typeId)->submit()->getRoundingType()) {
            throw new Exception('Rounding type was not set to the expected value.');
        }

        return $this;
    }

    public function setRoundingMode($mode)
    {
        $this->backOffice->visitController('AdminPreferences');
        $prefs = $this->get('PageObject:BackOffice\AdminPreferencesPage');

        if (!array_key_exists($mode, $this->getKnownRoundingModes())) {
            throw new Exception(sprintf('Unknown rounding mode `%s`.', $mode));
        }

        $modeId = $this->getKnownRoundingModes()[$mode];

        if ($modeId != $prefs->setRoundingMode($modeId)->submit()->getRoundingMode()) {
            throw new Exception('Rounding mode was not set to the expected value.');
        }

        return $this;
    }
}
