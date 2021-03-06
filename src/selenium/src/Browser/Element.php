<?php

namespace PrestaShop\Selenium\Browser;

use WebDriverElement;

use PrestaShop\Selenium\Browser\Exception\ElementNotFoundException;

class Element implements ElementInterface
{
	private $nativeElement;
	private $browser;

	public function __construct(WebDriverElement $nativeElement, BrowserInterface $browser)
	{
		$this->nativeElement = $nativeElement;
		$this->browser = $browser;
	}

	public function getAttribute($attributeName)
	{
		return $this->nativeElement->getAttribute($attributeName);
	}

	public function getValue()
	{
		return $this->getAttribute('value');
	}

	public function sendKeys($keys)
	{
		$this->nativeElement->sendKeys($keys);

		return $this;
	}

	public function fillIn($value)
	{
		$this->nativeElement->clear()->sendKeys($value);

		return $this;
	}

	public function getText()
	{
		return trim($this->nativeElement->getText());
	}

	public function find($selector, array $options = array())
	{
		$options['baseElement'] = $this;

		return $this->browser->find($selector, $options);
	}

	public function all($selector)
	{
		try {
			return $this->find($selector, ['unique' => false]);
		} catch (ElementNotFoundException $e) {
			return [];
		}
	}

	public function getTagName()
	{
		return $this->nativeElement->getTagName();
	}

	public function isDisplayed()
	{
		return $this->nativeElement->isDisplayed();
	}

	public function isEnabled()
	{
		return $this->nativeElement->isEnabled();
	}

	public function isSelected()
	{
		return $this->nativeElement->isSelected();
	}

	public function getNativeElement()
	{
		return $this->nativeElement;
	}

	public function click()
	{
		$this->nativeElement->click();

		return $this;
	}
}
