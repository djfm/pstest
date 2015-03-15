<?php

namespace PrestaShop\ConfMap\Tests;

use PHPUnit_Framework_TestCase;

use PrestaShop\ConfMap\Tests\Fixtures\EmptyConf;
use PrestaShop\ConfMap\Tests\Fixtures\SomeConfNoRoot;
use PrestaShop\ConfMap\Tests\Fixtures\SomeConf;

class ConfigurationTest extends PHPUnit_Framework_TestCase
{
    public function test_getRoot_When_UnDefined()
    {
        $conf = new EmptyConf();
        $this->assertEquals(null, $conf->getRoot());
    }

    public function test_getRoot_When_Defined()
    {
        $conf = new SomeConf();
        $this->assertEquals('some_conf', $conf->getRoot());
    }

    public function test_ListOfPropertiesToConfigure_isRetrievedFromAnnotations_When_UnDefined()
    {
        $conf = new EmptyConf();
        $this->assertEquals([], $conf->getProperties());
    }

    public function test_ListOfPropertiesToConfigure_isRetrievedFromAnnotations_When_Defined()
    {
        $conf = new SomeConf();
        $this->assertEquals([
            'a' => 'a',
            'b' => 'b',
            'database_user' => 'database.user'
        ], $conf->getProperties());
    }

    public function test_loadArray()
    {
        $conf = new SomeConf();
        $conf->loadArray(['some_conf' => ['a' => 42, 'b' => 'hi', 'database' => ['user' => 'bob']]]);

        $this->assertEquals(42, $conf->getA());
        $this->assertEquals('hi', $conf->getB());
        $this->assertEquals('bob', $conf->getDatabaseUser());
    }

    public function test_loadArray_NoRoot()
    {
        $conf = new SomeConfNoRoot();
        $conf->loadArray(['a' => 42, 'b' => 'hi']);

        $this->assertEquals(42, $conf->getA());
        $this->assertEquals('hi', $conf->getB());
    }

    /**
     * @expectedException Exception
     */
    public function test_loadFile_FileNotFound()
    {
        $conf = new SomeConf();
        $conf->loadFile('not_a_file');
    }

    public function test_loadFile_JSON()
    {
        $conf = new SomeConf();
        $conf->loadFile(__DIR__ . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'conf.json');

        $this->assertEquals(1, $conf->getA());
        $this->assertEquals(2, $conf->getB());
        $this->assertEquals('root', $conf->getDatabaseUser());
    }
}
