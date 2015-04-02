<?php

namespace PrestaShop\TestRunner\Tests;

use PHPUnit_Framework_TestCase;

use PrestaShop\TestRunner\ExceptionTransformer;

use Exception;

class ExceptionTransformerTest extends PHPUnit_Framework_TestCase
{
    public function test_exception_is_serialized_even_if_stack_has_closure()
    {
        $willFail = function () {
            throw new Exception;
        };

        $e = null;
        try {
            $willFail(function () {
                // closure passed as arguments will make the trace unserializable
            });
        } catch (Exception $thrown) {
            $e = $thrown;
        }

        $et = new ExceptionTransformer;
        $e = $et->makeExceptionSerializable($e);

        // no assertion here, but this call will throw an exception
        // if makeExceptionSerializable did not work.
        serialize($e);
    }
}
