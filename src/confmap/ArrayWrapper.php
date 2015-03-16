<?php

namespace PrestaShop\ConfMap;

class ArrayWrapper
{
    private $array;

    public function __construct(array $array)
    {
        $this->array = $array;
    }

    public static function splitPath($path)
    {
        if ('' === $path) {
            return null;
        }

        return explode(".", $path);
    }

    private static function _get(array $source, array $pathComponents, $default, &$found)
    {
        if (empty($pathComponents)) {
            return $default;
        }

        $component = array_shift($pathComponents);

        if (!array_key_exists($component, $source)) {
            return $default;
        }

        $source = $source[$component];

        if (empty($pathComponents)) {
            $found = true;
            return $source;
        }

        return self::_get($source, $pathComponents, $default, $found);
    }

    public function get($path, $default = null, $returnWasFound = false)
    {
        $pathComponents = self::splitPath($path);

        $found = false;
        $value = $default;

        if ($pathComponents) {
            $value = self::_get($this->array, $pathComponents, $default, $found);
        }

        if ($returnWasFound) {
            return [$value, $found];
        }

        return $value;
    }

    private static function _set(array &$source, array $pathComponents, $value)
    {
        if (empty($pathComponents)) {
            $source = $value;
        } else {
            $key = array_shift($pathComponents);
            if (!array_key_exists($key, $source) || !is_array($source[$key])) {
                $source[$key] = [];
            }
            self::_set($source[$key], $pathComponents, $value);
        }
    }

    public function set($path, $value)
    {
        $pathComponents = self::splitPath($path);

        if ($pathComponents) {
            $value = self::_set($this->array, $pathComponents, $value);
        }

        return $this;
    }
}
