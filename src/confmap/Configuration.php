<?php

namespace PrestaShop\ConfMap;

use ReflectionClass;
use ReflectionProperty;

use Closure;
use Exception;

class Configuration
{
    public function getRoot()
    {
        $refl = new ReflectionClass($this);
        $comment = $refl->getDocComment();
        $m = [];
        if (preg_match('/^\s*\*\s*@root\s+(\w+)\s*$/mi', $comment, $m)) {
            return $m[1];
        } else {
            return null;
        }
    }

    public function getProperties()
    {
        $refl = new ReflectionClass($this);

        $properties = [];

        foreach ($refl->getProperties() as $prop) {
            $comment = $prop->getDocComment();
            $m = [];
            if (preg_match('/^\s*\*\s*@conf(?:\s+(\w+(?:\.\w+)*))?\s*$/mi', $comment, $m)) {

                $mappedBy = $prop->getName();

                if (!empty($m[1])) {
                    $mappedBy = $m[1];
                }

                $properties[$prop->getName()] = $mappedBy;
            }
        }

        return $properties;
    }

    public function loadArray(array $options)
    {
        $wrapper = new ArrayWrapper($options);

        $root = $this->getRoot();

        foreach ($this->getProperties() as $field => $mappedBy) {

            $path = $mappedBy;

            if ($root !== null) {
                $path = $root . '.' . $path;
            }

            list($value, $found) = $wrapper->get($path, null, true);

            if ($found) {

                // Bind a Closure to be able to change private properties,
                // useful in case no setter was defined.

                $setter = function () use ($field, $value) {
                    $this->$field = $value;
                };

                $setter = Closure::bind($setter, $this, $this);

                $setter();
            }
        }

        return $this;
    }

    public function loadFile($path)
    {
        if (!is_file($path) || !is_readable($path)) {
            throw new Exception(sprintf('File `%s` not found or unreadable.', $path));
        }

        $data = json_decode(file_get_contents($path), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception(sprintf('Invalid JSON found in file `%s`.', $path));
        }

        return $this->loadArray($data);
    }
}
