<?php

class PrestaShop_IoC_Container
{
    private $bindings = array();
    private $instances = array();

    public function bind($serviceName, $constructor, $shared = false)
    {
        $this->bindings[$serviceName] = array(
            'constructor' => $constructor,
            'shared' => $shared
        );

        return $this;
    }

    public function make($serviceName)
    {
        if (!array_key_exists($serviceName, $this->bindings)) {
            throw new Exception(
                sprintf(
                    'Don\'t know how to make a `%s`.',
                    $serviceName
                )
            );
        }

        $binding = $this->bindings[$serviceName];

        if ($binding['shared'] && array_key_exists($serviceName, $this->instances)) {
            return $this->instances[$serviceName];
        } else {
            $service = call_user_func($binding['constructor']);

            if ($binding['shared']) {
                $this->instances[$serviceName] = $service;
            }

            return $service;
        }
    }
}
