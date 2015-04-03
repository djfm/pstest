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
        try {
            $refl = new ReflectionClass($className);
        } catch (ReflectionException $re) {
            throw new PrestaShop_IoC_Exception(sprintf('This doesn\'t seem to be a class name: `%s`.', $className));
        }

        $args = [];

        if ($refl->isAbstract()) {
            throw new PrestaShop_IoC_Exception(sprintf('Cannot build abstract class: `%s`.', $className));
        }

        $classConstructor = $refl->getConstructor();

        if ($classConstructor) {
            foreach ($classConstructor->getParameters() as $param) {
                $paramClass = $param->getClass();
                if ($paramClass) {
                    $args[] = $this->doMake($param->getClass()->getName(), $alreadySeen);
                } else if ($param->isDefaultValueAvailable()) {
                    $args[] = $param->getDefaultValue();
                } else {
                    throw new PrestaShop_IoC_Exception(sprintf('Cannot build a `%s`.', $className));
                }
            }
        }

        return $refl->newInstanceArgs($args);
    }

    private function doMake($serviceName, array $alreadySeen = array())
    {
        if (array_key_exists($serviceName, $alreadySeen)) {
            throw new PrestaShop_IoC_Exception(sprintf(
                'Cyclic dependency detected while building `%s`.',
                $serviceName
            ));
        }

        $alreadySeen[$serviceName] = true;

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
                $service = $this->makeInstanceFromClassName($constructor, $alreadySeen);
            }

            if ($binding['shared']) {
                $this->instances[$serviceName] = $service;
            }

            return $service;
        }
    }

    public function make($serviceName)
    {
        return $this->doMake($serviceName, []);
    }
}
