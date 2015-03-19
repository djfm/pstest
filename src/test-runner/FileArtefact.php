<?php

namespace PrestaShop\TestRunner;

use Exception;

class FileArtefact
{
    private $name;
    private $path;

    public function __construct($name, $path)
    {
        $this->setName($name);
        $this->setPath($path);
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    public function getContents()
    {
        if (!is_readable($this->path)) {
            throw new Exception(
                sprintf(
                    'Could not read file `%s`.',
                    $this->path
                )
            );
        }

        return file_get_contents($this->path);
    }
}
