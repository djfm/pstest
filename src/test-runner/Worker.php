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

        $plan = unserialize($plan);

        return $this->processPlan($plan);
    }

    private function processPlan(TestPlanInterface $plan)
    {
        $aggregator = new TestAggregator;

        $plan->setTestAggregator($aggregator);

        $plan->run();

        $this->client->send(['type' => 'plan finished', 'aggregator' => serialize($aggregator)]);
    }
}
