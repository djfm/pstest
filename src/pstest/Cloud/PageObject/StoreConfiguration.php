<?php

namespace PrestaShop\PSTest\Cloud\PageObject;

use Exception;

class StoreConfiguration
{
    private $browser;

    public function __construct($browser)
    {
        $this->browser = $browser;
    }

    public function setCountry($name)
    {
        $countries = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'countries.json'), true);

        foreach ($countries as $country) {
            if ($country['name'] === $name) {
                $this->browser->select('#inputCountry', $country['id']);
                return $this;
            }
        }

        throw new Exception(sprintf('Unknown country %s.', $name));
    }

    public function setQualification($id)
    {
        $this->browser->select('#id_qualification', $id);
        return $this;
    }

    public function submit()
    {
        $this->browser->click('#submit-form');
        return new YourAccount($this->browser);
    }
}
