<?php

namespace PrestaShop\TestRunner;

class TestMessage
{
    private $text;
    private $type;

    public function __construct($text, $type)
    {
        $this->text = $text;
        $this->type = $type;
    }

    public function __toString()
    {
        return "[{$this->type}] {$this->text}";
    }

    public function getText()
    {
        return $this->text;
    }

    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

}
