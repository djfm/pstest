<?php

namespace PrestaShop\PSTest\Shop;

use Exception;

abstract class Shop
{
    private $services = [];

    private $instances = [];

    public function get($serviceAsString)
    {
        if (array_key_exists($serviceAsString, $this->instances)) {
            return $this->instances[$serviceAsString];
        } else if (array_key_exists($serviceAsString, $this->services)) {

            $builder = $this->services[$serviceAsString];

            $serviceInstance = call_user_func_array(
                $builder['constructor'],
                $builder['dependencies']
            );

            $this->instances[$serviceAsString] = $serviceInstance;

            return $serviceInstance;
        }

        throw new Exception(
        sprintf(
                'Service `%s` is nowhere to be found.',
                $serviceAsString
            )
        );
    }

    public function registerService($name, array $dependencies, $constructor)
    {
        $this->services[$name] = [
            'dependencies' => $dependencies,
            'constructor' => $constructor
        ];

        return $this;
    }
}
