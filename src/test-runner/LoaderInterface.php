<?php

namespace PrestaShop\TestRunner;

interface LoaderInterface
{
    /**
     * Must return objects implementing the TestPlanInterface.
     */
    public function loadTestPlansFromFile(
        $file,
        array $classesInFile,
        array $filters = array()
    );
}
