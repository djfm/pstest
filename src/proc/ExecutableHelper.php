<?php

namespace PrestaShop\Proc;

use PrestaShop\FileSystem\FileSystemHelper as FS;

class ExecutableHelper
{
    private static function getPathDirectories()
    {
        if (Platform::isWindows()) {
            $pathVar = 'Path';
            $separator = ';';
        } else {
            $pathVar = 'PATH';
            $separator = ':';
        }

        $env = Platform::getEnv();
        if (!isset($env[$pathVar])) {
            return [];
        }
        $path = $env[$pathVar];

        return array_filter(explode($separator, $path), function ($entry) {
            return $entry !== "";
        });
    }

    public static function inPath($executable)
    {
        if (Platform::isWindows()) {
            if (!preg_match('/\.exe$/', $executable)) {
                $executable .= '.exe';
            }
        }

        foreach (self::getPathDirectories() as $directory) {
            if (is_executable(FS::join($directory, $executable))) {
                return true;
            }
        }

        return false;
    }
}
