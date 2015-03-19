<?php

namespace PrestaShop\TestRunner;

class TestAggregatorSummarizer
{
    private $aggregators = [];

    public function addAggregator(TestAggregator $aggregator)
    {
        $this->aggregators[] = $aggregator;

        return $this;
    }

    public function getStatistics()
    {
        
    }
}
