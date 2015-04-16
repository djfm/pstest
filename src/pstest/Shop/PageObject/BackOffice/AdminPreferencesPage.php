<?php

namespace PrestaShop\PSTest\Shop\PageObject\BackOffice;

use PrestaShop\PSTest\Shop\PageObject\PageObject;

class AdminPreferencesPage extends PageObject
{
    public function setRoundingType($typeId)
    {
        $this->browser->select('#PS_ROUND_TYPE', $typeId);

        return $this;
    }

    public function getRoundingType()
    {
        return $this->browser->getSelectedValue('#PS_ROUND_TYPE');
    }

    public function setRoundingMode($modeId)
    {
        $this->browser->select('#PS_PRICE_ROUND_MODE', $modeId);

        return $this;
    }

    public function getRoundingMode()
    {
        return $this->browser->getSelectedValue('#PS_PRICE_ROUND_MODE');
    }

    public function submit()
    {
        $this->browser->clickButtonNamed('submitOptionsconfiguration')->checkStandardFormFeedback();
        return $this;
    }
}
