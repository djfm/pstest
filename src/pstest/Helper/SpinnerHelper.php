<?php

namespace PrestaShop\PSTest\Helper;

use Exception;

class SpinnerHelper
{
    public static function assertNoException(callable $test, $timeout_s = 5, $interval_ms = 500, $message = null)
    {
        $h = new Spinner($test, $timeout_s, $interval_ms, $message);
        return $h->assertNoException();
    }

    public static function assertTrue(callable $test, $timeout_s = 5, $interval_ms = 500, $message = null)
    {
        $h = new Spinner($test, $timeout_s, $interval_ms, $message);
        return $h->assertTrue();
    }
}
