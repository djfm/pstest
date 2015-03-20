<?php

namespace PrestaShop\TestRunner;

interface TestPlanInterface
{
    public function setTestAggregator(TestAggregator $aggregator);
    public function run();
    public static function serializeAsArray(TestPlanInterface $testPlan);
    public static function unserializeFromArray(array $array);
}
