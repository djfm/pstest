<?php

namespace PrestaShop\PSTest\Cloud\PageObject;

use Exception;

class YourAccount
{
    private $browser;

    public function __construct($browser)
    {
        $this->browser = $browser;
    }

    public function setFirstName($name)
    {
        $this->browser->fillIn('#firstname', $name);
        return $this;
    }

    public function setLastName($name)
    {
        $this->browser->fillIn('#lastname', $name);
        return $this;
    }

    public function setPassword($password)
    {
        $this->browser
             ->fillIn('#password', $password)
             ->fillIn('#confirm_password', $password)
        ;
        return $this;
    }

    public function acceptNewsletter($yes = false)
    {
        $this->browser->checkbox('#newsletter', $yes);
        return $this;
    }

    public function acceptTermsAndConditions($yes = false)
    {
        $this->browser->checkbox('#cgv', $yes);
        return $this;
    }

    public function submit()
    {
        $this->browser->click('[name="submitstep2"]');
        try {
            $this->browser->waitFor('.link-resend');
        } catch (Exception $e) {
            throw new Exception('Did not appear to reach the "Store created" page.');
        }

        return $this;
    }
}
