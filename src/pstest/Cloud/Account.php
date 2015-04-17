<?php

namespace PrestaShop\PSTest\Cloud;

class Account
{
    private $customer;

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }
}
