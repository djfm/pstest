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

    public function test_get()
    {
        $conf = new SomeConf();
        $this->assertEquals('default_a', $conf->get('some_conf.a'));
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

    public function test_dumpArray()
    {
        $conf = new SomeConf();
        $this->assertEquals([
            'some_conf' => [
                'a' => 'default_a',
                'b' => 'default_b',
                'database' => ['user' => null]
            ]
        ], $conf->dumpArray());
    }

    public function test_dumpFile()
    {
        $conf = new SomeConf();
        $conf->setA('not default_a');
        $conf->setB('not default_b');
        $conf->database_user = 'merlin';

        $file = tempnam(sys_get_temp_dir(), 'conftest');
        $conf->dumpFile($file);

        $otherConf = new SomeConf();
        $otherConf->loadFile($file);

        $this->assertEquals(
            $conf->dumpArray(),
            $otherConf->dumpArray()
        );

        unlink($file);
    }

    public function test_dumpFile_existingFile_does_NOT_overwrite_other_data()
    {
        $multiconfPath = __DIR__ . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'multiconf.json';
        $currentConf = new SomeConf;
        $currentConf->loadFile($multiconfPath);

        $dumpedconfPath = tempnam(sys_get_temp_dir(), 'conftest');
        copy($multiconfPath, $dumpedconfPath);

        $currentConf->dumpFile($dumpedconfPath);

        $written = json_decode(file_get_contents($dumpedconfPath), true);
        $this->assertEquals('me', $written['dont_touch']);

        unlink($dumpedconfPath);
    }
}
