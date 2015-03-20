<?php

namespace PrestaShop\TestRunner\TestCase;

use PrestaShop\TestRunner\LoaderInterface;

class TestCaseLoader implements LoaderInterface
{
    public function loadTestPlansFromFile($filePath, array $classesInFile)
    {
        $testPlans = [];

        foreach ($classesInFile as $className) {
            $masterInstance = new $className;
            if ($masterInstance instanceof PrestaShop\TestRunner\TestCase\TestCase) {
                $contexts = $masterInstance->contextProvider();

                foreach ($contexts as $context) {
                    $testPlan = new $className;
                    $testPlan->setContext($context)->setFilePath($filePath);
                }
            }
        }

        return $testPlans;
    }
}
