<?php

namespace PrestaShop\TestRunner\Command;

class CLIOption
{
    private $name;
    private $shortcut;
    private $mode;
    private $description;
    private $default;

    public function __construct(
        $name,
        $shortcut = null,
        $mode = null,
        $description = '',
        $default = null
    )
    {
        $this->name = $name;
        $this->shortcut = $shortcut;
        $this->mode = $mode;
        $this->description = $description;
        $this->default = $default;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getShortcut()
    {
        return $this->shortcut;
    }

    public function getMode()
    {
        return $this->mode;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getDefault()
    {
        return $this->default;
    }
}
