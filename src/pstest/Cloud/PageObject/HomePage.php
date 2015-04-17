<?php
namespace PrestaShop\PSTest\Cloud\PageObject;

use Exception;

class HomePage
{
    private $browser;

	public static $twoLetterLanguageCodes = [
		'en' => 'English',
		'fr' => 'Français',
		'es' => 'Español',
		'it' => 'Italiano',
		'pt' => 'Portuguese',
		'nl' => 'Dutch'
	];

    /*
	public function getNewStoreURL()
	{
		return rtrim($this->getURL(), '/') . '/en/create-your-online-store';
	}*/

    public function __construct($browser)
    {
        $this->browser = $browser;
    }

	public function setLanguage($twoLetterCode)
	{
		if (empty(static::$twoLetterLanguageCodes[$twoLetterCode])) {
			throw new Exception("Invalid language code: $twoLetterCode.");
		}
		$wantedLanguage   = mb_strtolower(trim(static::$twoLetterLanguageCodes[$twoLetterCode]), 'UTF-8');
		$currentLanguage  = mb_strtolower(trim($this->browser->getText('#menu-language')), 'UTF-8');
		// Can't set the language to the current one, so return if we're already OK.
		if ($currentLanguage === $wantedLanguage) {
			return $this;
		}

        $this->browser
		     ->click('#menu-language a.dropdown-toggle')
		     ->click('#menu-language [title="'.$twoLetterCode.'"]')
             ->waitFor('#menu-language')
        ;

        $currentLanguage = mb_strtolower(trim($this->browser->getText('#menu-language')), 'UTF-8');

        if ($currentLanguage !== $wantedLanguage) {
			throw new Exception("Language did not change, got `$currentLanguage` instead of `$wantedLanguage`!");
		}

		return $this;
	}

    public function chooseCloud()
    {
        $this->browser->click('a.btn.get-me-started[href="#"]');
        return new OnboardingBanner($this->browser);
    }
}
