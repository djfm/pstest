<?php

namespace PrestaShop\TestRunner;

/**
 * This is all static because for once there is no choice: loaded classes cannot be unloaded,
 * they're static by essence.
 */
class ClassDiscoverer
{
    private static $declaredClasses = [];

    public static function getDeclaredClasses($filePath)
    {
        if (!isset(self::$declaredClasses[$filePath])) {
            if (preg_match('/\.php$/', $filePath)) {
                $oldClasses = get_declared_classes();
                include_once $filePath;
                $newClasses = array_diff(get_declared_classes(), $oldClasses);
                self::$declaredClasses[$filePath] = array_values($newClasses);
            } else {
                self::$declaredClasses[$filePath] = [];
            }
        }
        return self::$declaredClasses[$filePath];
    }
}
