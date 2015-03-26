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
    protected $shop_language = 'en';

    /**
     * @conf shop.country
     */
    protected $shop_country = 'us';

    /**
     * @conf shop.name
     */
    protected $shop_name = 'PrestaShop';

    /**
     * @conf employee.firstname
     */
    protected $employee_firstname = 'John';

    /**
     * @conf employee.lastname
     */
    protected $employee_lastname = 'Doe';

    /**
     * @conf employee.email
     */
    protected $employee_email = 'pub@prestashop.com';

    /**
     * @conf employee.password
     */
    protected $employee_password = '123456789';
}
