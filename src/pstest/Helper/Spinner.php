<?php

namespace PrestaShop\PSTest\Helper;

use Exception;

class Spinner
{
    private $test;
    private $timeout_s;
    private $interval_ms;
    private $message;

    private $abort_on = [];

    public function __construct(callable $test, $timeout_s = 5, $interval_ms = 500, $message = null)
    {
        $this->test = $test;
        $this->timeout_s = $timeout_s;
        $this->interval_ms = $interval_ms;
        $this->message = $message;
    }

    public function setTimeout($seconds)
    {
        $this->timeout_s = $seconds;
        return $this;
    }

    public function setInterval($milliseconds)
    {
        $this->interval_ms = $milliseconds;
        return $this;
    }

    public function assertNoException()
    {
        $startedAt = microtime(true);

        $outOfTime = function () use ($startedAt) {
            return (microtime(true) > $startedAt + $this->timeout_s);
        };

        $test = $this->test;

        for (;;) {
            try {
                $test($this);
            } catch (Exception $e) {
                if ($outOfTime() || $this->mustAbortOn($e)) {
                    if ($this->message) {
                        throw new Exception($this->message);
                    } else {
                        throw $e;
                    }
                }
            }

            if ($outOfTime()) {
                break;
            }

            usleep($this->interval_ms * 1000);
        }
    }

    public function assertTrue()
    {
        $message = null;

        if (!$this->message) {
            $message = 'Callable dit not return `true` in the expected time.';
        }

        $actualTest = $this->test;

        $this->test = function () use ($message, $actualTest) {
            if ($actualTest($this) !== true) {
                throw new Exception($message);
            }
        };

        return $this->assertNoException();
    }

    public function abortOnException($className)
    {
        $this->abort_on[$className] = true;
        return $this;
    }

    public function abortOnAnyException()
    {
        return $this->abortOnException('Exception');
    }

    private function mustAbortOn(Exception $e)
    {
        foreach ($this->abort_on as $className => $abort) {
            if ($abort && ($e instanceof $className)) {
                return true;
            }
        }

        return false;
    }
}
