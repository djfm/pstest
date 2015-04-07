<?php

namespace PrestaShop\PSTest\Shop\PageObject\BackOffice\Products;

use PrestaShop\PSTest\Shop\PageObject\BackOfficePageObject;
use PrestaShop\PSTest\Helper\SpinnerHelper as Spin;

class QuantitiesPage extends BackOfficePageObject
{
    public function setQuantity($quantity)
    {
        /**
         * Ok, so this next part is tricky!
         *
         * We need to detect the successful change of the quantity field.
         * So, we trigger the change by injecting javascript, and watch the DOM
         * to detect the success notification (div.growl.growl-notice).
         *
         * We need to watch the DOM before triggering the event, because to make
         * things easier, the notification is transient.
         *
         * This is a bit suboptimal because it fails to emulate exactly the user behaviour,
         * but it should be close enough. If anybody has a better idea, please PR!
         *
         */
        $qset = <<<'EOS'
            var quantity = arguments[0];
            var done = arguments[1]; // Selenium wraps us inside a function, and we need to call done when done.
            var observer = new MutationObserver(function () {
                if ($('#growls .growl.growl-notice').length > 0) {
                    done();
                    observer.disconnect();
                }
            });
            observer.observe(document.documentElement, {childList: true, subtree: true});
            $("#qty_0 input").val(quantity);
            $("#qty_0 input").trigger("change");
EOS;

        $this->browser->setScriptTimeout(5);
        Spin::assertNoException(function () use ($qset, $quantity) {
            $this->browser->executeAsyncScript($qset, [$quantity]);
        }, 20, 2000, 'Could not set product quantity!');

        return $this;
    }

    public function getQuantity()
    {
        return $this->browser->getValue('#qty_0 input');
    }
}
