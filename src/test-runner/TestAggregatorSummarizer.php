<?php

namespace PrestaShop\TestRunner;

use SimpleXMLElement;

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
                if (!$result->hasChildren()) {
                    $this->updateStatistics($result);
                }
            }
        }

        return $this->statistics;
    }

    public function forEachTestResult(callable $cb)
    {
        foreach ($this->aggregators as $aggregator) {
            foreach ($aggregator->getTestResults() as $result) {
                if (!$result->hasChildren()) {
                    $cb($result, $aggregator->getContext());
                }
            }
        }

        return $this;
    }

    public function getJUnitXMLReportAsString()
    {
        $suites = new SimpleXMLElement('<testsuites></testsuites>');

        $resultsBySuite = [];

        $this->forEachTestResult(function (TestResult $result, array $context) use (&$resultsBySuite) {
            ksort($context);
            $suiteKey = $result->getPackage() . ' ' . $result->getTestSuite() . md5(serialize($context));
            if (!array_key_exists($suiteKey, $resultsBySuite)) {
                $resultsBySuite[$suiteKey] = [
                    'name' => $result->getTestSuite(),
                    'package' => $result->getPackage(),
                    'results' => [],
                    'context' => $context
                ];
            }
            $resultsBySuite[$suiteKey]['results'][] = $result;
        });

        $id = 0;
        foreach ($resultsBySuite as $data) {
            $suite = $suites->addChild('testsuite');
            $suite->addAttribute('package', $data['package']);
            $suite->addAttribute('id', $id);
            $suite->addAttribute('name', $data['name']);
            $suite->addAttribute('hostname', 'localhost');

            $suite->addAttribute('tests', count($data['results']));
            $suiteStartTime = 0;
            $totalTime = 0;

            $failures = 0;
            $errors = 0;

            $properties = $suite->addChild('properties');
            foreach ($data['context'] as $name => $value) {
                $property = $properties->addChild('property');
                $property->addAttribute('name', $name);
                $property->addAttribute('value', $value);
            }

            foreach ($data['results'] as $result) {
                $resultStartTime = (int)$result->getStartTime();
                if (0 === $suiteStartTime || $resultStartTime < $suiteStartTime) {
                    $suiteStartTime = $resultStartTime;
                }

                $status = $result->getStatus();

                $test = $suite->addChild('testcase');
                $test->addAttribute('classname', $result->getBaseName());
                $test->addAttribute('name', $result->getFullName());
                $test->addAttribute('time', sprintf('%f',$result->getTotalTime()));

                if (!$status->isSuccessful()) {
                    if ($status->isError()) {
                        $errors++;
                        $issue = $test->addChild('error');
                    } else if ($status->isFailure()) {
                        $failures++;
                        $issue = $test->addChild('failure');
                    }
                    $issue->addAttribute('type', $status->getCode());
                }
            }

            $suite->addAttribute('timestamp', date('Y-m-d\TH:i:s', $suiteStartTime));
            $suite->addAttribute('failures', 1);
            $suite->addAttribute('errors', 0);
            $suite->addAttribute('time', $totalTime);

            $suite->addChild('system-out');
            $suite->addChild('system-err');

            $id++;
        }

        $dom = dom_import_simplexml($suites)->ownerDocument;
        $dom->formatOutput = true;
        return $dom->saveXML();
    }
}
