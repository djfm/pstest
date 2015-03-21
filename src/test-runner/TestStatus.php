<?php

namespace PrestaShop\TestRunner;

/**
 * This class represents the status of a test.
 *
 * $success is a boolean that marks, well, success or failure.
 * $code refines the result.
 *
 * We can take some guidance from PHPUnit here:
 *
 * Quote taken from https://phpunit.de/manual/current/en/textui.html:
 *
 * PHPUnit distinguishes between failures and errors.
 * A "failure" is a violated PHPUnit assertion such as a failing assertEquals() call.
 * An "error" is an unexpected exception or a PHP error.
 * Sometimes this distinction proves useful since errors tend to be easier to fix than failures.
 * If you have a big list of problems, it is best to tackle the errors first and see if you have any failures left when they are all fixed.
 */

class TestStatus
{
    private $success;
    private $code;

    public function __construct($success, $code)
    {
        $this->success = $success;
        $this->code = $code;
    }

    public function isSuccessful()
    {
        return $this->success;
    }

    public function getCode()
    {
        return $this->code;
    }
}
