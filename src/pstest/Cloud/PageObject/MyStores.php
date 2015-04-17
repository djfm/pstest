<?php

namespace PrestaShop\PSTest\Cloud\PageObject;

use Exception;

class MyStores
{
    private $browser;

    public function __construct($browser)
    {
        $this->browser = $browser;
    }

	public function getStoreWidgetRoot($storeName = null)
	{
		if (null === $storeName) {
			return $this->browser->find('div.listingStore');
		}

		$xpath = '{xpath}//div[contains(@class, "listingStore") and .//h5[contains(., "' . $storeName . '")]]';
		return $this->browser->find($xpath);
	}

	public function getFrontOfficeURL($storeName)
	{
		return $this->getStoreWidgetRoot($storeName)->find('span.domain')->getText();
	}

	public function getBackOfficeURL($storeName)
	{
		return $this->getStoreWidgetRoot($storeName)->find('a[data-sel="bo-link"]')->getAttribute('href');
	}
}
