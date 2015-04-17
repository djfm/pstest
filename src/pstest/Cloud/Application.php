<?php

namespace PrestaShop\PSTest\Cloud;

use Exception;

use PrestaShop\PSTest\Cloud\Customer;
use PrestaShop\PSTest\Shop\RemoteShop;

use PrestaShop\PSTest\Cloud\PageObject\HomePage;
use PrestaShop\PSTest\Cloud\PageObject\MyStores;

use PrestaShop\PSTest\Shop\DefaultSettings;

class Application
{
    private $url;
    private $browser;

    public static $expectedActivationLinkTitle = [
		'en' => 'Activate my account',
		'fr' => 'Activer mon compte',
		'es' => 'Activar mi cuenta',
		'it' => 'Attivare il mio account',
		'pt' => 'Activar a minha conta',
		'nl' => 'Activeer mijn account'
	];

    public function __construct($url, $browser)
    {
        $this->url = rtrim($url, '/');
        $this->browser = $browser;
    }

    public function getURL()
    {
        return $this->url;
    }

    public function createAccountAndShop(Customer $customer, $onboardingLanguage, $shopCountryName, RemoteShop $shop)
    {
        $this->browser->visit($this->url);
        $homePage = new HomePage($this->browser);
        $homePage->setLanguage($onboardingLanguage);

        $homePage->chooseCloud()
                 ->setStoreName($shop->getName())
                 ->setEmailAddress($customer->getEmailAddress())
                 ->submit()
                 ->setCountry($shopCountryName)
                 ->setQualification(8) // "Just trying"
                 ->submit()
                 ->setFirstName($customer->getFirstName())
                 ->setLastName($customer->getLastName())
                 ->setPassword($customer->getPassword())
                 ->acceptNewsletter(false)
                 ->acceptTermsAndConditions(true)
                 ->submit();
        ;

        $linkTitle = @static::$expectedActivationLinkTitle[$onboardingLanguage];

        if (!$linkTitle) {
            throw new Exception(
                sprintf('Don\'t know what activation title to expect for language `%s`.', $onboardingLanguage)
            );
        }

        $activationLink = $customer->waitForActivationEmailAndGetActivationLink($linkTitle);

        $this->browser->visit($activationLink);

        $myStores = new MyStores($this->browser);

        $shop->setFrontOfficeURL($myStores->getFrontOfficeURL($shop->getName()));
        $shop->setBackOfficeURL($myStores->getBackOfficeURL($shop->getName()));

        $defaults = new DefaultSettings;
        $defaults->shop_name = $shop->getName();
        $defaults->employee_firstname = $customer->getFirstName();
        $defaults->employee_lastname = $customer->getLastName();
        $defaults->employee_email = $customer->getEmailAddress();
        $defaults->employee_password = $customer->getPassword();
        $shop->setDefaults($defaults);
    }
}
