<?php

namespace PrestaShop\TestRunner;

use djfm\SocketRPC\Client;

class Worker
{
    private $serverAddress;
    private $client;

    public function setServerAddress($serverAddress)
    {
        $this->serverAddress = $serverAddress;

        return $this;
    }

    public function run()
    {
        $this->client = new Client;
        $this->client->connect($this->serverAddress);

        $plan = $this->client->query(['type' => 'get plan']);

        if (null === $plan) {
            return 1;
        }

        $planData = unserialize($plan);

        $plan = $planData['plan'];
        $pluginData = $planData['runnerPluginData'];

        return $this->processPlan($plan, $pluginData);
    }

    public function onTestEvent(TestEvent $event, array $context)
    {
        $this->client->send([
            'type' => 'test event',
            'event' => serialize($event),
            'context' => $context
        ]);

        return $this;
    }

    private function processPlan(TestPlanInterface $plan, array $pluginData)
    {
        $aggregator = new TestAggregator;

        $aggregator->addEventListener([$this, 'onTestEvent']);

        $plan->setTestAggregator($aggregator);

        foreach ($pluginData as $pluginName => $data) {
            $plan->setRunnerPluginData($pluginName, $data);
        }

        $plan->run();

        $this->client->send(['type' => 'plan finished', 'aggregator' => serialize($aggregator)]);
    }
}
