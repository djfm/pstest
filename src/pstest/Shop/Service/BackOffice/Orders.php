<?php

namespace PrestaShop\PSTest\Shop\Service\BackOffice;

use PrestaShop\PSTest\Shop\BackOfficeService;

use PrestaShop\PSTest\Shop\PageObject\BackOffice\OrderPage;

class Orders extends BackOfficeService
{
    public function visitById($id)
    {
        $this->backOffice->visitController('AdminOrders', ['vieworder', 'id_order' => $id]);
        return $this->get('PageObject:BackOffice\OrderPage');
    }
}
