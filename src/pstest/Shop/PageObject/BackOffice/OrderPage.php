<?php

namespace PrestaShop\PSTest\Shop\PageObject\BackOffice;

use Exception;

use PrestaShop\PSTest\Shop\PageObject\BackOfficePageObject;

class OrderPage extends BackOfficePageObject
{
    public function validate($targetOrderStateId = 2)
    {
        $this->browser
             ->jqcSelect('#id_order_state', $targetOrderStateId)
             ->clickButtonNamed('submitState');

        return $this;
    }

    public function getInvoiceLink()
    {
        return $this->browser->getAttribute('[data-selenium-id="view_invoice"]', 'href');
    }

    public function getInvoiceData()
    {
        $invoice_json_link = $this->getInvoiceLink().'&debug=1';
        $text = $this->browser->xhr($invoice_json_link);
        $arr = json_decode($text, true);
        if (!is_array($arr)) {
            throw new Exception('Invalid invoice JSON found.');
        }
        return $arr;
    }

    public function getInvoicePDF()
    {
        return $this->browser->xhr($this->getInvoiceLink());
    }
}
