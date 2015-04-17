<?php

namespace PrestaShop\PSTest\Shop;

use PrestaShop\ConfMap\Configuration;

/**
 * @root defaults
 */
class DefaultSettings extends Configuration
{
    /**
     * @conf shop.language
     */
    public $shop_language = 'en';

    /**
     * @conf shop.country
     */
    public $shop_country = 'us';

    /**
     * @conf shop.name
     */
    public $shop_name = 'PrestaShop';

    /**
     * @conf employee.firstname
     */
    public $employee_firstname = 'John';

    /**
     * @conf employee.lastname
     */
    public $employee_lastname = 'Doe';

    /**
     * @conf employee.email
     */
    public $employee_email = 'pub@prestashop.com';

    /**
     * @conf employee.password
     */
    public $employee_password = '123456789';

    /**
     * @conf customer.email
     */
    public $customer_email = 'pub@prestashop.com';

    /**
     * @conf customer.password
     */
    public $customer_password = '123456789';
}
