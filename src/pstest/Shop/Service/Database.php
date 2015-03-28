<?php

namespace PrestaShop\PSTest\Shop\Service;

use Exception;

use PrestaShop\PSTest\Shop\LocalShop;

use PrestaShop\PSTest\Helper\MySQL as DatabaseHelper;

class Database
{
    private $shop;
    private $db;

    public function __construct(LocalShop $shop, DatabaseHelper $db)
    {
        $this->shop = $shop;
        $this->db = $db;
    }

    public function drop()
    {
        $this->db->dropDatabase($this->shop->getSystemSettings()->getDatabaseName());

        return $this;
    }

    public function create()
    {
        $this->db->createDatabase($this->shop->getSystemSettings()->getDatabaseName());

        return $this;
    }
}