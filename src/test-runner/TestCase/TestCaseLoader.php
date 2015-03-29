<?php

namespace PrestaShop\TestRunner\TestCase;

use PrestaShop\TestRunner\LoaderInterface;
use PrestaShop\TestRunner\TestCase\TestCase;

class TestCaseLoader implements LoaderInterface
{
    private function contextIsExcludedByFilter(array $context, $filter)
    {
        $contextFilterRecognizer = '/^context:(\w+)=(.+)/i';
        $m = [];
        if (preg_match($contextFilterRecognizer, $filter, $m)) {
            $filterKey = $m[1];
            $filterValue = $m[2];

            if (array_key_exists($filterKey, $context)) {
                return $context[$filterKey] != $filterValue;
            }
        }

        return false;
    }

    private function filterOutContext(array $context, array $filters) {
        foreach ($filters as $filter) {
            if ($this->contextIsExcludedByFilter($context, $filter)) {
                return true;
            }
        }

        return false;
    }

    public function loadTestPlansFromFile($filePath, array $classesInFile, array $filters = array())
    {
        $testPlans = [];

        foreach ($classesInFile as $className) {
            $masterInstance = new $className;
            if ($masterInstance instanceof TestCase) {
                $contexts = $masterInstance->contextProvider();
                foreach ($contexts as $context) {

                    if ($this->filterOutContext($context, $filters)) {
                        continue;
                    }

                    $testPlan = new $className($filters);
                    $testPlan->setContext($context)->setFilePath($filePath);
                    $testPlans[] = $testPlan;
                }
            }
        }

        return $testPlans;
    }
}
