<?php

namespace PrestaShop\Proc;

class Platform
{
    public static function isWindows()
    {
        return preg_match('/^WIN/', PHP_OS);
    }

    public static function getEnv()
    {
        $env = [];

        foreach ([$_SERVER, $_ENV] as $source) {
            foreach ($source as $key => $value) {
                if (is_string($value)) {
                    $env[$key] = $value;
                }
            }
        }

        return $env;
    }
}
