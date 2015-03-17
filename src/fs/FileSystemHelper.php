<?php

namespace PrestaShop\FileSystem;

class FileSystemHelper
{
    public static function join(/* paths */)
    {
        return call_user_func_array([new FileSystem(), 'join'], func_get_args());
    }
}
