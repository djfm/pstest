<?php

namespace PrestaShop\Proc\Tests;

use PHPUnit_Framework_TestCase;

use PrestaShop\Proc\Proc;

class ProcTest extends PHPUnit_Framework_TestCase
{
    private function getCommandThatSleeps($seconds = 1)
    {
        return PHP_BINARY . ' ' . escapeshellarg(__DIR__ . DIRECTORY_SEPARATOR . 'sleeping_beauty.php') . ' ' . (int)$seconds;
    }

    private function getCommandWithOutput($output)
    {
        return PHP_BINARY . ' ' . escapeshellarg(__DIR__ . DIRECTORY_SEPARATOR . 'output.php') . ' ' . escapeshellarg($output);
    }

    public function test_run_BaseCase()
    {
        $proc = new Proc();
        $proc->setCommand($this->getCommandThatSleeps(1));
        $this->assertTrue($proc->start());
    }

    public function test_close_waitsForProcess()
    {
        $proc = new Proc();
        $proc->setCommand($this->getCommandThatSleeps(2));
        $tStart = time();
        $this->assertTrue($proc->start());
        $this->assertTrue($proc->close());
        $duration = time() - $tStart;
        $this->assertGreaterThan(0, $duration);
    }

    public function test_exit_code_available_after_close()
    {
        $proc = new Proc();
        $proc->setCommand($this->getCommandThatSleeps(1));
        $this->assertTrue($proc->start());
        $this->assertTrue($proc->close());
        $this->assertEquals(42, $proc->getExitCode());
    }

    public function test_run_getStatus()
    {
        $proc = new Proc();
        $proc->setCommand($this->getCommandThatSleeps(1))->start();
        $status = $proc->getStatus();
        $this->assertTrue($status['running']);
    }

    public function test_terminate()
    {
        $proc = new Proc();
        $tStart = time();
        $proc->setCommand($this->getCommandThatSleeps(10))->start();
        $proc->terminate();
        sleep(1);
        $this->assertEquals(false, $proc->isRunning());
        $this->assertLessThan(2, time() - $tStart);
    }

    public function test_exitCode_after_terminate()
    {
        $proc = new Proc();
        $proc->setCommand($this->getCommandThatSleeps(10))->start();
        $proc->terminate();
        sleep(1);
        $this->assertEquals(false, $proc->isRunning());
        $this->assertEquals(-1, $proc->getExitCode());
    }

    public function test_run_getExitCode()
    {
        $proc = new Proc();
        $proc->setCommand($this->getCommandThatSleeps(1))->start();
        $this->assertTrue($proc->isRunning());
        sleep(2);
        $this->assertFalse($proc->isRunning());
        $this->assertEquals(42, $proc->getExitCode());
        // doing it twice to check that our implementation is better than the standard one,
        // which would only get the real status the first time after the program exited.
        $this->assertEquals(42, $proc->getExitCode());
    }

    public function test_getChildren()
    {
        $proc = new Proc($this->getCommandThatSleeps(10));
        $proc->start();
        $pids = $proc->getChildren();
        $this->assertGreaterThan(0, count($pids));
        foreach ($pids as $pid) {
            $this->assertFalse($pid === $proc->getPID());
        }
    }
}
