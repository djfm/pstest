<?php

namespace PrestaShop\TestRunner;

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
