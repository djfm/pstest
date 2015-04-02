<?php

namespace PrestaShop\TestRunner;

use Exception;
use Closure;
use Serializable;

class ExceptionTransformer
{
    public function isSerializable($value)
    {
        if (is_scalar($value)) {
            return true;
        } else if (is_array($value)) {
            foreach ($value as $v) {
                if (!$this->isSerializable($v)) {
                    return false;
                }
            }

            return true;
        } else if (is_object($value)) {
            return $value instanceof Serializable;
        }

        return false;
    }

    public function makeExceptionSerializable(Exception $source)
    {
        $e = new Exception(
            $source->getMessage(),
            $source->getCode(),
            null
        );

        $privacyInvador = function () use ($e, $source) {

            $e->trace = $source->getTrace();

            foreach ($e->trace as &$row) {
                foreach ($row['args'] as &$arg) {
                    if (!$this->isSerializable($arg)) {
                        $arg = '{unserializable}';
                    }
                }
                unset($arg);
            }
            unset($row);

            $e->file = $source->getFile();
            $e->line = $source->getLine();
        };

        $privacyInvador = Closure::bind($privacyInvador, $this, $e);

        $privacyInvador();

        return $e;
    }
}
