<?php

namespace PrestaShop\TestRunner;

interface TestPlanInterface
{
    public function setTestAggregator(TestAggregator $aggregator);
    public function run();
}
