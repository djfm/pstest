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

    public function dump($target)
    {
        $this->db->dumpDatabase(
            $this->shop->getSystemSettings()->getDatabaseName(),
            $target
        );

        return $this;
    }

    public function load($source)
    {
        $this->db->loadDatabase(
            $this->shop->getSystemSettings()->getDatabaseName(),
            $source
        );

        return $this;
    }

    public function getPDO()
    {
        return $this->db->getPDO($this->shop->getSystemSettings()->getDatabaseName());
    }

    public function changePhysicalURI($uri)
    {
        $prefix = $this->shop->getSystemSettings()->getDatabaseTablesPrefix();

        $urls = $this->getPDO()->query(sprintf(
            'SELECT id_shop_url, physical_uri FROM %1$sshop_url',
            $prefix
        ));

        while (($url = $urls->fetch())) {
            $oldURI = $url['physical_uri'];
            $newURI = preg_replace('#(?:^|/)(?:\w+)/?(.*)#', '/' . $uri . '/' . '${1}', $oldURI);
            $this->getPDO()->exec(sprintf(
                'UPDATE %1$sshop_url SET physical_uri = \'%2$s\' WHERE id_shop_url = %3$d;',
                $prefix,
                $newURI,
                $url['id_shop_url']
            ));
        }
    }
}
