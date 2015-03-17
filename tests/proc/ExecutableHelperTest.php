<?php

namespace PrestaShop\Proc\Test;

use PHPUnit_Framework_TestCase;

use PrestaShop\Proc\Platform;
use PrestaShop\Proc\ExecutableHelper;

class ExecutableHelperTest extends PHPUnit_Framework_TestCase
{
    public function test_inPath()
    {
        if (Platform::isWindows()) {
            $this->assertTrue(ExecutableHelper::inPath('cmd'));
        } else {
            $this->assertTrue(ExecutableHelper::inPath('sh'));
        }
    }
}
