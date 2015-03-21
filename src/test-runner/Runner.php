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

    public function __construct()
    {
        $this->summarizer = new TestAggregatorSummarizer;
    }

    public function addTestPath($path)
    {
        $this->testPaths[] = $path;

        return $this;
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

    protected function done()
    {
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

    public function onTestEvent(TestEvent $event, array $context)
    {

    }
}
