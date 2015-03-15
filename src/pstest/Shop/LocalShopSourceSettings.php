<?php

namespace PrestaShop\PSTest\Shop;

class LocalShopSourceSettings
{
    private $path_to_shop_files;
    private $back_office_folder_name;
    private $installer_folder_name;

    public function getPathToShopFiles()
    {
        return $this->path_to_shop_files;
    }

    public function setPathToShopFiles($path_to_shop_files)
    {
        $this->path_to_shop_files = $path_to_shop_files;
        return $this;
    }

    public function getBackOfficeFolderName()
    {
        return $this->back_office_folder_name;
    }

    public function setBackOfficeFolderName($back_office_folder_name)
    {
        $this->back_office_folder_name = $back_office_folder_name;
        return $this;
    }

    public function getInstallerFolderName()
    {
        return $this->installer_folder_name;
    }

    public function setInstallerFolderName($installer_folder_name)
    {
        $this->installer_folder_name = $installer_folder_name;
        return $this;
    }
}
