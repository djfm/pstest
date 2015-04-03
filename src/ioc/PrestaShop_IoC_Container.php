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

    private function makeInstanceFromClassName($className, array $alreadySeen)
    {
        if (array_key_exists($className, $alreadySeen)) {
            throw new PrestaShop_IoC_Exception(sprintf(
                'Cyclic dependency detected while building `%s`.',
                $className
            ));
        }

        $alreadySeen[$className] = true;

        try {
            $refl = new ReflectionClass($className);
        } catch (ReflectionException $re) {
            throw new PrestaShop_IoC_Exception(sprintf('This doesn\'t seem to be a class name: `%s`.', $className));
        }

        $args = [];

        $classConstructor = $refl->getConstructor();

        if ($classConstructor) {
            foreach ($classConstructor->getParameters() as $param) {
                $paramClass = $param->getClass();
                if ($paramClass) {
                    $args[] = $this->makeInstanceFromClassName(
                        $param->getClass()->getName(),
                        $alreadySeen
                    );
                } else if ($param->isDefaultValueAvailable()) {
                    $args[] = $param->getDefaultValue();
                } else {
                    throw new PrestaShop_IoC_Exception(sprintf('Cannot build a `%s`.', $className));
                }
            }
        }

        return $refl->newInstanceArgs($args);
    }

    public function make($serviceName)
    {
        if (!array_key_exists($serviceName, $this->bindings)) {
            $this->bind($serviceName, $serviceName);
        }

        $binding = $this->bindings[$serviceName];

        if ($binding['shared'] && array_key_exists($serviceName, $this->instances)) {
            return $this->instances[$serviceName];
        } else {
            $constructor = $binding['constructor'];

            if (is_callable($constructor)) {
                $service = call_user_func($constructor);
            } else {
                // assume the $constructor is a class name
                $service = $this->makeInstanceFromClassName($constructor, []);
            }


            if ($binding['shared']) {
                $this->instances[$serviceName] = $service;
            }

            return $service;
        }
    }
}
