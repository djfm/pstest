<?php

namespace PrestaShop\TestRunner\Tests;

use PHPUnit_Framework_TestCase;

use PrestaShop\TestRunner\TestAggregator;

class TestAggregatorSummarizerTest extends PHPUnit_Framework_TestCase
{
    private function makeAggregator(array $statuses)
    {
        $agg = new TestAggregator();

        $n = 0;

        foreach ($statuses as $data)
        {
            for ($i = 0; $i < $data['count']; $i++) {
                $n++;
                $testName = 'test ' . $n;
                $agg->startTest($testName);
                $agg->endTest($testName, $data['success'], $data['status']);
            }
        }

        return $agg;
    }

    public function test_statistics_one_aggregator()
    {
        $this->makeAggregator([
            ['count' => 3, 'success' => true, 'status' => 'ok'],
            ['count' => 3, 'success' => true, 'status' => 'yep'],
            ['count' => 2, 'success' => false, 'status' => 'error'],
            ['count' => 5, 'success' => false, 'status' => 'failure']
        ]);

        $this->markTestIncomplete('In progress...');
    }
}
