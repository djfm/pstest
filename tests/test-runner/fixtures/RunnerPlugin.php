<?php

namespace PrestaShop\TestRunner\Tests\Fixtures;

use PrestaShop\TestRunner\RunnerPlugin as BasePlugin;

class RunnerPlugin extends BasePlugin
{
    public $hello;
    public $world;

    public function __construct()
    {
        $this->hello = 'hello';
        $this->world = 'world';
    }

    public function setup()
    {

    }

    public function teardown()
    {

    }
}
