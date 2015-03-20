<?php

namespace PrestaShop\TestRunner\TestCase;

use PrestaShop\TestRunner\LoaderInterface;
use PrestaShop\TestRunner\TestCase\TestCase;

class TestCaseLoader implements LoaderInterface
{
    public function loadTestPlansFromFile($filePath, array $classesInFile)
    {
        $testPlans = [];

        foreach ($classesInFile as $className) {

            // Abstract class TestCase might be detected upon loading a test, dont consider
            // it a TestCase.
            // Real tests subclass TestCase.
            if ($className === 'PrestaShop\TestRunner\TestCase\TestCase') {
                continue;
            }

            $masterInstance = new $className;
            if ($masterInstance instanceof TestCase) {
                $contexts = $masterInstance->contextProvider();
                foreach ($contexts as $context) {
                    $testPlan = new $className;
                    $testPlan->setContext($context)->setFilePath($filePath);
                    $testPlans[] = $testPlan;
                }
            }
        }

        return $testPlans;
    }
}
