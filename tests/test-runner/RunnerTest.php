<?php

namespace PrestaShop\TestRunner\Tests;

use PHPUnit_Framework_TestCase;

use PrestaShop\TestRunner\Runner;
use PrestaShop\TestRunner\Tests\Fixtures\RunnerPlugin;

class RunnerTest extends PHPUnit_Framework_TestCase
{
    private function getFixturePath($name)
    {
        return implode(DIRECTORY_SEPARATOR, [__DIR__, 'fixtures', $name]);
    }

    public function test_run_single_file()
    {
        $runner = new Runner();

        ob_start();
        $runner
            ->addTestPath(
                $this->getFixturePath('SmokeTest.php')
            )
            ->run();
        ob_end_clean();

        $stats = $runner->getSummarizer()->getStatistics();

        $this->assertEquals(12, $stats['ok']);
    }

    public function test_correct_error_and_skipped_count()
    {
        $runner = new Runner();

        ob_start();
        $runner
            ->addTestPath(
                $this->getFixturePath('FailingSmokeTest.php')
            )
            ->addTestPath(
                $this->getFixturePath('SmokeTest.php')
            )
            ->run();
        ob_end_clean();

        $stats = $runner->getSummarizer()->getStatistics();

        $this->assertEquals(16, $stats['ok']);
        $this->assertEquals(8, $stats['ko']);
        $this->assertEquals(4, $stats['details']['ko']['error']);
        $this->assertEquals(4, $stats['details']['ko']['skipped']);
    }

    public function test_plugins_are_executed()
    {
        $runner = new Runner();

        ob_start();
        $runner
            ->addTestPath(
                $this->getFixturePath('PluginTest.php')
            )
            ->run();
        ob_end_clean();

        $stats = $runner->getSummarizer()->getStatistics();

        $this->assertEquals(1, $stats['ok']);
        $this->assertCount(1, $runner->getPlugins());
    }
}
