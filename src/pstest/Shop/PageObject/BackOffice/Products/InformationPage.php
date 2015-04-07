<?php

namespace PrestaShop\PSTest\Shop\PageObject\BackOffice\Products;

use PrestaShop\PSTest\Shop\PageObject\BackOfficePageObject;

class InformationPage extends BackOfficePageObject
{
    public function setName($name)
    {
        $this->browser->fillIn($this->backOffice->i18nFieldName('#name'), $name);
        return $this;
    }

    public function getName()
    {
        return $this->browser->getValue($this->backOffice->i18nFieldName('#name'));
    }
}
