<?php

namespace PrestaShop\TestRunner;

class TestAggregatorSummarizer
{
    private $aggregators = [];

    private $statistics = [];

    public function addAggregator(TestAggregator $aggregator)
    {
        $this->aggregators[] = $aggregator;

        return $this;
    }

    private function initializeStatistics()
    {
        return [
            'ok' => 0,
            'ko' => 0,
            'total' => 0,
            'details' => [
                'ok' => [],
                'ko' => []
            ]
        ];
    }

    private function updateStatistics(TestResult $result)
    {
        $this->statistics['total']++;

        $successful = $result->getStatus()->isSuccessful();
        $code = $result->getStatus()->getCode();

        $success = $successful ? 'ok' : 'ko';

        $this->statistics[$success]++;

        if (!array_key_exists($code, $this->statistics['details'][$success])) {
            $this->statistics['details'][$success][$code] = 0;
        }

        $this->statistics['details'][$success][$code]++;
    }

    public function getStatistics()
    {
        $this->statistics = $this->initializeStatistics();

        foreach ($this->aggregators as $aggregator) {
            foreach ($aggregator->getTestResults() as $result) {
                $this->updateStatistics($result);
            }
        }

        return $this->statistics;
    }
}
