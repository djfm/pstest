<?php

namespace PrestaShop\PSTest\Cloud;

use Symfony\Component\DomCrawler\Crawler;
use PrestaShop\PSTest\Email\EmailReader;
use PrestaShop\PSTest\Helper\SpinnerHelper as Spin;

use PrestaShop\PSTest\Shop\Shop;

class Customer
{
    private $email;
    private $password;
    private $firstName = "Nhojj";
    private $lastName = "Öêdd Selenium";
    private $uid;
    private $shops = [];

    public function __construct($email, $password, $uid, EmailReader $emailReader)
    {
        $this->email = $email;
        $this->password = $password;
        $this->emailReader = $emailReader;
        $this->uid = $uid;
    }

    public function getEmailAddress()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getUID()
    {
        return $this->uid;
    }

    public function waitForActivationEmailAndGetActivationLink($expectedLinkTitle)
    {
        return Spin::assertTrue(function () use ($expectedLinkTitle) {
            $emails = $this->emailReader->readEmails($this->email);
            foreach ($emails as $email) {
                $crawler = new Crawler('', 'http://www.example.com'); // Crawler needs a URL, even though we don't use it
                $crawler->addHtmlContent($email['body']);
                $link = $crawler->selectLink($expectedLinkTitle);
                if ($link->count() > 0) {
                    return $link->link()->getURI();
                }
            }

            return false;
        }, 300, 2000, 'Did not get activation E-Mail in time.');
    }
}
