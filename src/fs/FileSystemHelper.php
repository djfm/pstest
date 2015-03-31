<?php

namespace PrestaShop\FileSystem;

class FileSystemHelper
{
    public static function join(/* paths */)
    {
        $args = func_get_args();
        if (count($args) === 1 && is_array($args[0])) {
            $args = $args[0];
        }

        return call_user_func_array([new FileSystem(), 'join'], $args);
    }
}
