<?php

namespace PrestaShop\TestRunner;

use Exception;

use PrestaShop\Proc\Proc;

use djfm\SocketRPC\Server;

class Runner
{
    private $testPaths = [];
    private $plansLeft;
    private $server;
    private $runningClients = [];

    private $maxWorkers = 1;

    private $summarizer;

    private $outputInterface;

    public function __construct()
    {
        $this->summarizer = new TestAggregatorSummarizer;
    }

    public function addTestPath($path)
    {
        $this->testPaths[] = $path;

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

    private function loadPlans()
    {
        $loader = new Loader();

        foreach ($this->testPaths as $path) {
            if (is_dir($path)) {
                throw new Exception('Loading folders is not implemented yet, sorry!');
            }

            $loader->loadFile($path);
        }

        $this->plansLeft = $loader->getTestPlans();

        return $this;
    }

    private function bindServerEvents()
    {
        $this->server
             ->on('tick'    , [$this, 'onServerTick'])
             ->on('query'   , [$this, 'onClientQuery'])
             ->on('send'    , [$this, 'onClientMessage'])
        ;
    }

    private function startServer()
    {
        $this->server = new Server();
        $this->server->bind();
        $this->bindServerEvents();
        $this->server->run();
    }

    private function getWorkerPath()
    {
        $path = __DIR__ . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'worker.php';

        if (!file_exists($path)) {
            throw new Exception(
                sprintf(
                    'Could not find worker script in `%s`.',
                    $path
                )
            );
        }

        return $path;
    }

    private function spawnClient()
    {
        $command = implode(' ', [
            PHP_BINARY,
            escapeshellarg($this->getWorkerPath()),
            escapeshellarg($this->server->getAddress())
        ]);

        $proc = new Proc($command);

        $this->runningClients[] = $proc;

        $proc->start();

        return $this;
    }

    public function run()
    {
        $this->loadPlans();
        $this->startServer();
        $this->done();
    }

    private function done()
    {
        $stats = $this->getSummarizer()->getStatistics();

        $pad = 15;

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

    private function cleanClients()
    {
        foreach ($this->runningClients as $key => $client) {
            if (!$client->isRunning()) {
                unset($this->runningClients[$key]);
            }
        }
    }

    private function spawnClients()
    {
        for ($i = count($this->runningClients); ($i < $this->maxWorkers) && !empty($this->plansLeft); ++$i) {
            $this->spawnClient();
        }

        return $this;
    }

    private function onPlanFinished(TestAggregator $aggregator)
    {
        $this->summarizer->addAggregator($aggregator);

        return $this;
    }

    /**
     * NOT part of the public API.
     * @internal
     */
    public function onClientQuery($query, callable $respond)
    {
        if (is_array($query) && array_key_exists('type', $query)) {
            if ($query['type'] === 'get plan') {
                if (!empty($this->plansLeft)) {
                    $respond(serialize(array_shift($this->plansLeft)));
                } else {
                    $respond(null);
                }
            }
        }
    }

    /**
     * NOT part of the public API.
     * @internal
     */
    public function onClientMessage($query)
    {
        if (is_array($query) && array_key_exists('type', $query)) {
            if ($query['type'] === 'plan finished') {
                $this->onPlanFinished(unserialize($query['aggregator']));
            } else if ($query['type'] === 'test event') {
                $event = unserialize($query['event']);
                $this->onTestEvent($event, $query['context']);
            }
        }
    }

    /**
     * NOT part of the public API.
     * @internal
     */
    public function onServerTick()
    {
        $this->cleanClients();
        $this->spawnClients();

        if (empty($this->runningClients) && empty($this->plansLeft)) {
            $this->server->stop();
        }
    }

    public function getSummarizer()
    {
        return $this->summarizer;
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

    public function onTestEvent(TestEvent $event, array $context)
    {
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
