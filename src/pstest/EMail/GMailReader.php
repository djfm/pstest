<?php

namespace PrestaShop\PSTest\EMail;

use PrestaShop\PSTest\Helper\SpinnerHelper as Spin;

class GMailReader implements EmailReader
{
	private $email;
	private $password;

	public function __construct($email, $password)
	{
		$this->email = $email;
		$this->password = $password;
	}

    public function getAddress()
    {
        return $this->email;
    }

	private function findPart($structure, $matcher)
	{
		if (!isset($structure->parts)) {
			return false;
		}
		// Search breadth first
		foreach ($structure->parts as $n => $subStructure) {
			if ($matcher($subStructure)) {
				return (string)($n + 1);
			}
		}

		// Not found? Go deeper
		foreach ($structure->parts as $n => $subStructure) {
			$subPart = $this->findPart($subStructure, $matcher);
			if (false !== $subPart) {
				return ($n + 1) . '.' . $subPart;
			}
		}
		return false;
	}

	public function readEmails($sentTo = null, $bodyPart = null)
	{
		$host = '{imap.gmail.com:993/imap/ssl}INBOX';

		$inbox = Spin::assertTrue(function () use ($host) {
			return @imap_open($host, $this->email, $this->password);
		}, 60, 10000, 'Could not connect to Imap server.');

		$emails = imap_search($inbox, 'TO '.($sentTo ? $sentTo : $this->email));
		if ($emails) {
			$messages = [];
			foreach ($emails as $n)
			{
				$structure = imap_fetchstructure($inbox, $n);
				if (!$bodyPart) {
					$part = $this->findPart($structure, function ($part) {
						return $part->subtype === 'HTML';
					});
				} elseif (is_callable($bodyPart)) {
					$part = $this->findPart($structure, $bodyPart);
				} else {
					$part = $bodyPart;
				}
				$hinfo = imap_headerinfo($inbox, $n);
				$subject = $hinfo->subject;
				$message = [
					'subject' => $subject,
					'body' => imap_fetchbody($inbox, $n, $part),
				];
				$messages[] = $message;
			}
			return $messages;
		} else {
			return [];
		}
	}

	public function ensureAnEmailIsSentTo($address, $timeout_in_seconds = 300, array $options = array())
	{
		Spin::assertTrue(function () use ($address, $options) {
			$emails = $this->readEmails($address);
			if (isset($options['body']['contains'])) {
				foreach ($emails as $email) {
					if (false !== strpos($email['body'], $options['body']['contains'])) {
						return true;
					}
				}
				return false;
			} else {
				return count($emails) > 0;
			}
		}, $timeout_in_seconds, 10000, "Did not get required email on $address after {$timeout_in_seconds}s.");

		return $this;
	}
}
