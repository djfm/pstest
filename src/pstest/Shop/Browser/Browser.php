<?php

namespace PrestaShop\PSTest\Shop\Browser;

use Exception;

use PrestaShop\Selenium\Browser\BrowserInterface;
use PrestaShop\PSTest\Shop\Shop;

class Browser
{
    private $browser;
    private $shop;

    public function __construct(BrowserInterface $browser, Shop $shop)
    {
        $this->browser = $browser;
        $this->shop = $shop;
    }

    public function __call($name, $arguments) {
        $res = call_user_func_array([$this->browser, $name], $arguments);

        if ($res === $this->browser) {
            return $this;
        } else {
            return $res;
        }
    }

    public function checkStandardFormFeedback($errorMessage = '')
    {
        if (
            true  !== $this->hasVisible('div.alert.alert-success') ||
            false !== $this->hasVisible('div.alert.alert-error')
        ) {
            if (!$errorMessage) {
                $errorMessage = 'The page either reported an error or did not confirm success.';
            }

            throw new Exception($errorMessage);
        }

        return $this;
    }

    public function toggle($name, $yesOrNo)
    {
        return $this->clickLabelFor($name . ($yesOrNo ? '_on' : '_off'));
    }

    public function getToggleValue($name)
    {
        return $this->find('#' . $name . '_on')->isSelected();
    }
}
