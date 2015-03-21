<?php

namespace PrestaShop\TestRunner;

class CLIRunner extends Runner
{
    private $outputInterface;

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

    private function displayDots()
    {

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

    protected function done()
    {
        parent::done();

        $stats = $this->getSummarizer()->getStatistics();

        $pad = 15;

        $this->writeln('');

        $this->displayDots();

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
    }

    public function onTestEvent(TestEvent $event, array $context)
    {
        parent::onTestEvent($event, $context);

        $display = $event->isStart() || $event->isEnd();

        if ($display) {

            if ($event->isStart()) {
                $eventType = 'Start     :';
            } elseif ($event->isEnd()) {
                $eventType = 'End';
                if ($event->getTestResult()->getStatus()->isSuccessful()) {
                    $eventType .= '   <fg=green>:-)</fg=green> :';
                } else {
                    $eventType .= '   <fg=red>:/(</fg=red> :';
                }
            }

            $this->writeln(
                sprintf(
                    '<info>[%1$s]</info> %2$s {%3$s} %4$s (%5$s)',
                    date('H:i:s', (int)$event->getEventTime()),
                    $eventType,
                    $this->flatArrayToString($context),
                    $this->nicerClassName($event->getTestResult()->getFullName()),
                    $this->flatArrayToString($event->getTestResult()->getArguments())
                )
            );
        }
    }
}
