<?php

namespace PrestaShop\TestRunner;

use Exception;
use ReflectionClass;

use PrestaShop\Proc\Proc;

use djfm\SocketRPC\Server;

class Runner
{
    private $testPaths = [];
    protected $plansLeft;
    private $server;
    private $runningClients = [];
    private $pluginOptions = [];
    private $filters = [];

    private $maxWorkers = 1;

    private $summarizer;
    private $plugins = [];
    protected $informationOnly = false;
    protected $startedAt = 0;
    protected $endedAt = 0;


    public function __construct()
    {
        $this->summarizer = new TestAggregatorSummarizer;
    }

    public function setPluginOptions(array $options) {
        $this->pluginOptions = $options;
        return $this;
    }

    public function setFilters(array $filters)
    {
        $this->filters = $filters;
        return $this;
    }

    public function setMaxWorkers($p)
    {
        $this->maxWorkers = $p;
        return $this;
    }

    public function addTestPath($path)
    {
        $this->testPaths[] = $path;
        return $this;
    }

    public function setInformationOnly($flag)
    {
        $this->informationOnly = $flag;
        return $this;
    }

    private function loadPlans()
    {
        $loader = new Loader();

        $loader->setFilters($this->filters);

        foreach ($this->testPaths as $path) {
            if (is_dir($path)) {
                throw new Exception('Loading folders is not implemented yet, sorry!');
            }

            $loader->loadFile($path);
        }

        $this->plansLeft = $loader->getTestPlans();

        return $this;
    }

    private function loadPlugins()
    {
        $unique_plugins = [];

        foreach ($this->plansLeft as $plan) {
            $plugins = $plan->getRunnerPlugins();
            foreach ($plugins as $name => $plugin) {

                if (!($plugin instanceof RunnerPlugin)) {
                    throw new Exception(
                        sprintf('Class `%s` is not an instance of PrestaShop\TestRunner\RunnerPlugin.', get_class($plugin))
                    );
                }

                $hash = md5(serialize($plugin));
                if (!array_key_exists($hash, $unique_plugins)) {
                    $unique_plugins[$hash] = [
                        'plugin' => $plugin,
                        'planReferences' => []
                    ];
                }
                $unique_plugins[$hash]['planReferences'][] = [
                    'plan' => $plan,
                    'name' => $name
                ];
            }
        }

        $this->plugins = $unique_plugins;

        return $this;
    }

    private function setupPlugins()
    {
        foreach ($this->plugins as $pluginConf) {

            $className = get_class($pluginConf['plugin']);
            $options = [];
            if (array_key_exists($className, $this->pluginOptions)) {
                $options = $this->pluginOptions[$className];
            }

            $pluginConf['plugin']->setup($options);
        }

        return $this;
    }

    private function teardownPlugins()
    {
        foreach ($this->plugins as $pluginConf) {
            $pluginConf['plugin']->teardown();
        }

        return $this;
    }

    public function getPlugins()
    {
        return $this->plugins;
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
        $this->startedAt = microtime(true);
        $this->loadPlans();

        if (!$this->informationOnly) {
            $this->loadPlugins();
            $this->setupPlugins();
            $this->startServer();
            $this->done();
            $this->teardownPlugins();
        } else {
            $this->done();
        }
    }

    protected function done()
    {
        $this->endedAt = microtime(true);
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
                    $respond(serialize($this->getPlanForWorker()));
                } else {
                    $respond(null);
                }
            }
        }
    }

    private function getPlanForWorker()
    {
        $plan = array_shift($this->plansLeft);

        $pluginDataToSend = [];

        foreach ($this->plugins as $pluginConf) {
            foreach ($pluginConf['planReferences'] as $planAndName) {
                if ($planAndName['plan'] === $plan) {
                    $pluginData = $pluginConf['plugin']->getRunnerPluginData();
                    $pluginDataToSend[$planAndName['name']] = $pluginData;
                }
            }
        }

        return ['plan' => $plan, 'runnerPluginData' => $pluginDataToSend];
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
