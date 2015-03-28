<?php

namespace PrestaShop\FileSystem;

use Exception;

use Symfony\Component\Finder\Finder;

class FileSystem
{
    private $separators;
    private $separatorsArray = ['/', '\\'];

    public function __construct()
    {
        $this->separators = implode($this->separatorsArray);
    }

    private function joinTwo($a, $b)
    {
        return rtrim(str_replace($this->separatorsArray, DIRECTORY_SEPARATOR, $a), $this->separators) . DIRECTORY_SEPARATOR . trim($b, $this->separators);
    }

    public function join(/* variable arguments list: 0, 1 or more path parts */)
    {
        $args = func_get_args();

        if (count($args) === 0) {
            return realpath('.');
        } else if (count($args) === 1) {
            return $this->joinTwo(realpath('.'), $args[0]);
        } else if (count($args) === 2) {
            return $this->joinTwo($args[0], $args[1]);
        } else {
            return $this->joinTwo(array_shift($args), call_user_func_array([$this, 'join'], $args));
        }
    }

    public function getFinderIterator($sourceDir)
    {
        $finder = new Finder();

        $finder
        ->in($sourceDir)
        ->ignoreVCS(true)
        ->sortByType()
        ;

        return $finder;
    }

    public function cpr($sourceDir, $targetDir)
    {
        foreach ($this->getFinderIterator($sourceDir) as $info) {
            $from = $info->getRealPath();
            $to = $this->join($targetDir, $info->getRelativePathname());

            if (is_dir($from)) {
                if (!@mkdir($to, 0777, true)) {
                    throw new Exception(sprintf('Could not create directory `%s`.', $to));
                }
            } else {
                if (!@copy($from, $to)) {
                    throw new Exception(sprintf('Could not copy file `%1$s` to %2$s.', $from, $to));
                }
            }

            if (!@chmod($to, 0777)) {
                throw new Exception(sprintf('Could chmod 777 file `%s`.', $to));
            }
        }

        if (!@chmod($targetDir, 0777)) {
            throw new Exception(sprintf('Could chmod 777 file `%s`.', $to));
        }
    }
}
