<?php

namespace PrestaShop\PSTest\Tests;

use Exception;

use PHPUnit_Framework_TestCase;

use PrestaShop\PSTest\Helper\SpinnerHelper as Spin;

class SpinnerTest extends PHPUnit_Framework_TestCase
{
    public function test_assertNoException_When_NoException()
    {
        Spin::assertNoException(function () {

        }, 0);
    }

    /**
     * @expectedException Exception
     */
    public function test_assertNoException_When_AnException()
    {
        Spin::assertNoException(function () {
            throw new Exception;
        }, 0.01);
    }

    public function test_assertNoException_When_NoExceptionAfterDelay()
    {
        $tStart = time();
        Spin::assertNoException(function () use ($tStart) {
            if (time() - $tStart < 0.5) {
                throw new Exception('Should Not Break Test');
            }
        }, 1);
    }

    public function test_assertNoException_Throws_Original_Exception()
    {
        $caught = false;

        try {
            Spin::assertNoException(function () {
                throw new Exception('Original Message');
            }, 1);
        } catch (Exception $e) {
            $caught = true;
            $this->assertEquals('Original Message', $e->getMessage());
        }

        $this->assertTrue($caught);
    }

    public function test_assertTrue_OK()
    {
        Spin::assertTrue(function () {
            return true;
        }, 0.1);
    }

    /**
     * @expectedException Exception
     */
    public function test_assertTrue_KO()
    {
        Spin::assertTrue(function () {
            return false;
        }, 0.1);
    }

    public function test_assertTrue_When_TrueAfterDelay()
    {
        $tStart = time();
        Spin::assertNoException(function () use ($tStart) {
            if (time() - $tStart < 1) {
                return false;
            }
            return true;
        }, 2);
    }

    /**
     * @expectedException Exception
     */
    public function test_assertNoException_testCalledEvenIfNoTimeout()
    {
        Spin::assertNoException(function () {
            throw new Exception();
        }, 0);
    }

    public function test_abortOn_Exception()
    {
        $tStart = time();

        try {
            Spin::assertNoException(function ($spinner) {
                $spinner->abortOnException('Exception');
                throw new Exception();
            }, 2);
        } catch (Exception $e){
            // expected
        }

        $this->assertLessThan(1, time() - $tStart);
    }

    public function test_abortOn_AnyException()
    {
        $tStart = time();

        try {
            Spin::assertNoException(function ($spinner) {
                $spinner->abortOnAnyException();
                throw new Exception();
            }, 2);
        } catch (Exception $e){
            // expected
        }

        $this->assertLessThan(1, time() - $tStart);
    }
}
