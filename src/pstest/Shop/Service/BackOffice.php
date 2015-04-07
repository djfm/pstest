<?php

namespace PrestaShop\PSTest\Shop\Service;

use Exception;

use PrestaShop\PSTest\Shop\Shop;

use PrestaShop_IoC_Container;

use PrestaShop\PSTest\Shop\Service\BackOffice\Taxes as TaxesService;
use PrestaShop\PSTest\Shop\PageObject\BackOffice\AdminLocalizationPage;

class BackOffice
{
    private $shop;
    private $browser;

    private $menuLinks = [];
    private $loggedIn = false;

    private $defaultLanguage = null;

    public function __construct(Shop $shop)
    {
        $this->shop = $shop;
        $this->browser = $shop->getBrowser();

        $this->registerServices();
    }

    private function registerServices()
    {
        $this->shop->getContainer()->bind(
            'taxes',
            'PrestaShop\PSTest\Shop\Service\BackOffice\Taxes',
            true
        );

        $this->shop->getContainer()->bind(
            'localization',
            'PrestaShop\PSTest\Shop\Service\BackOffice\Localization',
            true
        );

        $this->shop->getContainer()->bind(
            'carriers',
            'PrestaShop\PSTest\Shop\Service\BackOffice\Carriers',
            true
        );

        $this->shop->getContainer()->bind(
            'products',
            'PrestaShop\PSTest\Shop\Service\BackOffice\Products',
            true
        );
    }

    public function get($serviceName)
    {
        return $this->shop->getContainer()->make($serviceName);
    }

    public function login($email = null, $password = null, $stayLoggedIn = true)
    {
        if (null === $email) {
            $email = $this->shop->getDefaults('employee.email');
        }

        if (null === $password) {
            $password = $this->shop->getDefaults('employee.password');
        }

        $browser = $this->shop->getBrowser();

        $browser->visit(
            $this->shop->getBackOfficeURL()
        );

        $controller = $browser->getURLParameter('controller');
        if ('AdminLogin' !== $controller) {
            throw new Exception(sprintf(
                'Expected to be on AdminLogin controller, but it seems we\'re somewhere else (%s).',
                $controller
            ));
        }

        $browser
        ->fillIn('#email', $email)
        ->fillIn('#passwd', $password);

        if ($stayLoggedIn) {
            $browser->clickLabelFor('stay_logged_in');
        }

        $browser->clickButtonNamed('submitLogin');

        $this->loggedIn = true;

        $this->crawlMenuLinks();
        $this->discoverDefaultLanguage();

        return $this;
    }

    private function requireLogin()
    {
        if (!$this->loggedIn) {
            throw new Exception('This action requires you to be logged in to the Back-Office.');
        }

        return $this;
    }

    public function visitController($name, array $extraURLParameters = array())
    {
        $this->requireLogin();

        if (!array_key_exists($name, $this->menuLinks)) {
            throw new Exception(sprintf(
                'Unknown AdminController `%s`.',
                $name
            ));
        }

        $url = $this->menuLinks[$name];

        foreach ($extraURLParameters as $key => $value) {
            if (is_string($key)) {
                $url .= '&' . $key . '=' . $value;
            } else {
                $url .= '&' . $value;
            }
        }

        $this->shop->getBrowser()->visit($url);

        return $this;
    }

    private function crawlMenuLinks()
    {
        $this->menuLinks = [];
        foreach ($this->shop->getBrowser()->all('li.maintab a') as $link) {
            $m = [];
            $url = $link->getAttribute('href');
            if (preg_match('/\bcontroller=(\w+)/', $url, $m)) {
                $controller = $m[1];
                $this->menuLinks[$controller] = $url;
            }
        }

        // Sanity check
        if (count($this->menuLinks) < 50) {
            throw new Exception('Did not find enough menu links in the Back-Office, something might be wrong.');
        }

        return $this;
    }

    public function discoverDefaultLanguage()
    {
        $url = $this->browser->getCurrentURL();
        $this->visitController('AdminLocalization');
        $loc = $this->shop->getContainer()->make(
            'PrestaShop\PSTest\Shop\PageObject\BackOffice\AdminLocalizationPage'
        );
        $this->defaultLanguage = $loc->getDefaultLanguage();
        $this->browser->visit($url);

        return $this;
    }

    /**
     * This adds the correct suffix to the passed-in $field
     * name, taking into account the language of the Back-Office.
     */
    public function i18nFieldName($field)
    {
        if (null === $this->defaultLanguage) {
            $this->discoverDefaultLanguage();
        }

        return $field . '_' . $this->defaultLanguage;
    }
}
