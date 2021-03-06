<?php

namespace PrestaShop\TestRunner;

use Exception;

class CLIRunner extends Runner
{
    private $outputInterface;
    private $writeJUnitXMLReportAs = null;

    public function setJUnitXMLReport($fileName)
    {
        $this->writeJUnitXMLReportAs = $fileName;
        return $this;
    }

    public function setOutputInterface($outputInterface)
    {
        $this->outputInterface = $outputInterface;
        return $this;
    }

    private function write($str)
    {
        if ($this->outputInterface) {
            $this->outputInterface->write($str);
        } else {
            echo $str;
        }
    }

    private function writeln($str)
    {
        if ($this->outputInterface) {
            $this->outputInterface->writeln($str);
        } else {
            echo $str . "\n";
        }
    }

    private function flatArrayToString(array $arr)
    {
        $parts = [];

        foreach ($arr as $key => $value) {
            if (is_scalar($value)) {
                $parts[] = $key . ': ' . (string)$value;
            }
        }

        return implode(', ', $parts);
    }

    private function nicerClassName($str)
    {
        $m = [];
        if (preg_match('/^(\w+(?:\\\\\w+)+\\\)(\w+)(.*)/', $str, $m)) {
            return '<comment>' . $m[1] . ' </comment>' . $m[2] . $m[3];
        }

        return $str;
    }

    private function printException(Exception $e, $paddingString)
    {
        $this->writeln(
            sprintf(
                '%1$s<comment>Message:</comment> %2$s',
                $paddingString, str_replace("\n", "\n$paddingString         ", $e->getMessage())
            )
        );
        $this->printExceptionTrace($e, $paddingString);
    }

    private function nicerPath($str)
    {
        return dirname($str) . DIRECTORY_SEPARATOR . '<options=bold>' . basename($str) . '</options=bold>';
    }

    private function longestCommonPrefix(array $strings)
    {
        $prefix = null;

        foreach ($strings as $str) {

            if (null === $str) {
                continue;
            }

            if (null === $prefix) {
                $prefix = $str;
            } else {
                $newPrefix = '';
                for ($c = 0; $c < min(strlen($prefix), strlen($str)); ++$c) {
                    if ($prefix[$c] === $str[$c]) {
                        $newPrefix .= $prefix[$c];
                    }
                }
                $prefix = $newPrefix;
                if ($prefix === '') {
                    break;
                }
            }
        }

        return $prefix;
    }

    private function printExceptionTrace(Exception $e, $paddingString)
    {
        $trace = $e->getTrace();

        $trace[0]['file'] = $e->getFile();
        $trace[0]['line'] = $e->getLine();

        if (!isset($trace[0]['function'])) {
            $trace[0]['function'] = '???';
        }

        // strip common prefix in file paths for optimized display
        $prefix = $this->longestCommonPrefix(array_map(function ($line) {
            return isset($line['file']) ? $line['file'] : null;
        }, $trace));
        $trace = array_map(function ($line) use ($prefix) {
            if (isset($line['file'])) {
                $line['file'] = substr($line['file'], strlen($prefix));
            }
            return $line;
        }, $trace);

        foreach ($trace as $l => $line) {

            $codeLocation = '<options=bold>' . $line['function'] . '</options=bold>';
            if (array_key_exists('class', $line)) {
                $codeLocation = $line['class'] . $line['type'] . $codeLocation;
            }

            $file = isset($line['file']) ? $this->nicerPath($line['file']) : '[unknown]';

            $in = sprintf(
                '%1$s<comment>In     :</comment> %2$s [%3$s:%4$s]',
                $paddingString, $codeLocation,
                $file, (isset($line['line']) ? $line['line'] : '[unknown]')
            );

            if ($l !== count($trace) - 1 ) {
                $in .= ' <comment>↓</comment>';
            }

            $this->writeln($in);
        }

        $this->writeln(
            sprintf(
                '%1$s<comment>(paths above are relative to: %2$s)</comment>',
                $paddingString, $prefix
            )
        );
    }

    private function displayProblems()
    {
        $this->getSummarizer()->forEachTestResult(function (TestResult $res, array $context) {
            if (!$res->getStatus()->isSuccessful()) {
                $this->writeln('');

                $id = $this->getTestIdentifierString($res, $context);
                $this->writeln('<error>Problem!</error> ' . $id);

                foreach ($res->getEvents() as $event) {
                    if ($event->hasException()) {
                        $this->writeln('');
                        $this->printException($event->getException(), '    ');
                    }
                }

                if ($res->getStatus()->getCode() === 'skipped') {
                    $this->writeln('');
                    $this->writeln('    <options=bold>Test skipped</options=bold>');
                }
            }
        });

        $this->writeln('');
    }

    private function displayDots()
    {
        $this->getSummarizer()->forEachTestResult(function (TestResult $res) {
            if ($res->getStatus()->isSuccessful()) {
                $this->write('.');
            } elseif ($res->getStatus()->getCode() === 'skipped') {
                $this->write('S');
            } else {
                $this->write('E');
            }
        });

        $this->writeln('');
    }

    protected function done()
    {
        parent::done();

        if ($this->informationOnly) {
            $this->writeln('Not running any tests because the --info flag was provided.');

            $count = 0;
            foreach ($this->plansLeft as $plan) {
                $count += $plan->getTestsCount();
            }

            $this->writeln(sprintf('Would have run %d test(s).', $count));

            return;
        }

        $stats = $this->getSummarizer()->getStatistics();

        $pad = 15;

        $this->writeln("\n<options=bold>Tests completed, summary follows:</options=bold>");

        $this->displayProblems();

        $this->writeln('');
        $this->displayDots();
        $this->writeln('');

        $this->writeln(
            sprintf(
                '<comment>' . str_pad('Took', $pad) . ': %ds</comment>',
                (int)($this->endedAt - $this->startedAt)
            )
        );

        $this->writeln('');

        $this->writeln(
            sprintf(
                str_pad('Total', $pad).': %d',
                $stats['total']
            )
        );

        $this->writeln(
            sprintf(
                str_pad('Successful', $pad).': %d',
                $stats['ok']
            )
        );

        $this->writeln(
            sprintf(
                str_pad('Failed', $pad).': %d',
                $stats['ko']
            )
        );

        if ($this->writeJUnitXMLReportAs) {
            $report = $this->getSummarizer()->getJUnitXMLReportAsString();
            file_put_contents($this->writeJUnitXMLReportAs, $report);
            $this->writeln(sprintf(
                "\n<comment>Wrote JUnit XML Report to: %s</comment>"
            , $this->writeJUnitXMLReportAs));
        }
    }

    private function getTestIdentifierString(TestResult $result, array $context)
    {
        return sprintf(
            '<options=underscore>Context</options=underscore>: {%1$s} <options=underscore>Test</options=underscore>: %2$s (%3$s)',
            $this->flatArrayToString($context),
            $this->nicerClassName($result->getFullName()),
            $this->flatArrayToString($result->getArguments())
        );
    }

    public function onTestEvent(TestEvent $event, array $context)
    {
        parent::onTestEvent($event, $context);

        $display = $event->isStart() || $event->isEnd() || $event->hasException() || $event->hasMessage();

        if ($display) {

            if ($event->isStart()) {
                $eventType = 'Start     :';
            } elseif ($event->isEnd()) {
                $eventType = 'End';
                if ($event->getTestResult()->getStatus()->isSuccessful()) {
                    $eventType .= '   <fg=green>:-D</fg=green> :';
                } else {
                    $eventType .= '   <fg=red>:<(</fg=red> :';
                }
            } elseif ($event->hasException()) {
                $eventType = '<error>Problem</error>   !';
            } else if ($event->hasMessage()) {
                $eventType = 'Info      :';
            }

            $this->writeln(
                sprintf(
                    '<info>[%1$s]</info> %2$s %3$s',
                    date('H:i:s', (int)$event->getEventTime()),
                    $eventType,
                    $this->getTestIdentifierString($event->getTestResult(), $context)
                )
            );

            if ($event->hasException()) {
                $this->printException($event->getException(), str_pad('', 21) . '| ');
            } else if ($event->hasMessage()) {
                $this->writeln(str_pad('', 21) . '| <comment>' . $event->getMessage()->getText() . '</comment>');
            }
        }
    }
}
