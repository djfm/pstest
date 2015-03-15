<?php

namespace PrestaShop\FileSystem\Tests;

use PHPUnit_Framework_TestCase;

use PrestaShop\FileSystem\FileSystem;

class FileSystemTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->fs = new FileSystem;
        $this->old_cwd = getcwd();
    }

    public function tearDown()
    {
        chdir($this->old_cwd);
    }

    public function test_join_NoArg_Gets_CWD()
    {
        chdir(__DIR__);
        $this->assertEquals(__DIR__, $this->fs->join());
    }

    public function test_join_OneArg_Gets_RelativeToCWD()
    {
        chdir(__DIR__);
        $this->assertEquals(__DIR__ . DIRECTORY_SEPARATOR . 'b', $this->fs->join('b'));
    }

    public function test_join_twoArgs()
    {
        $this->assertEquals('a' . DIRECTORY_SEPARATOR . 'b', $this->fs->join('a', 'b'));
    }

    public function test_join_threeArgs()
    {
        $this->assertEquals('a' . DIRECTORY_SEPARATOR . 'b' . DIRECTORY_SEPARATOR . 'c', $this->fs->join('a', 'b', 'c'));
    }

    public function separatorsExamples()
    {
        return [
            ['a/', '/b'],
            ['a//', '//b'],
            ['a\\', '//b/'],
            ['a\\', '/\\/b'],
        ];
    }

    /**
     * @dataProvider separatorsExamples
     */
    public function test_join_separatorsAreTrimmed($a, $b)
    {
        $this->assertEquals('a' . DIRECTORY_SEPARATOR . 'b', $this->fs->join($a, $b));
    }

    public function test_join_Leading_separator_is_kept()
    {
        $this->assertEquals(DIRECTORY_SEPARATOR . 'a' . DIRECTORY_SEPARATOR . 'b', $this->fs->join('/a', 'b'));
    }

    public function test_join_Leading_separator_is_normalized()
    {
        $wrongSeparator = DIRECTORY_SEPARATOR === '/' ? '\\' : '/';

        $this->assertEquals(DIRECTORY_SEPARATOR . 'a' . DIRECTORY_SEPARATOR . 'b', $this->fs->join($wrongSeparator . 'a', 'b'));
    }
}
