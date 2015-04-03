<?php

namespace PrestaShop\PSTest\Shop\BrowserExtension;

use PrestaShop\PSTest\Shop\Shop;

class ErrorChecker
{
    private $shop;

    public function __construct(Shop $shop)
    {
            $this->shop = $shop;
    }

    public function getBrowser()
    {
        return $this->shop->getBrowser();
    }

    public function checkStandardFormFeedback($errorMessage = '')
    {
        if (
            true  !== $this->getBrowser()->hasVisible('div.alert.alert-success') ||
            false !== $this->getBrowser()->hasVisible('div.alert.alert-error')
        ) {
            if (!$errorMessage) {
                $errorMessage = 'The page either reported an error or did not confirm success.';
            }

            throw new Exception($errorMessage);
        }

        return $this;
    }
}
