<?php

namespace PrestaShop\TestRunner\Tests;

use PHPUnit_Framework_TestCase;

use PrestaShop\TestRunner\Runner;

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
}
