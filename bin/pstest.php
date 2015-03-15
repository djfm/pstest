<?php
@ini_set('display_errors', 'on');

$autoloadPath = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'vendor', 'autoload.php']);

if (!file_exists($autoloadPath)) {
    throw new Exception(sprintf('Could not find composer autoload in `%s`, did you run composer install?'), $autoloadPath);
}

require $autoloadPath;

$app = new PrestaShop\PSTest\CLIApplication;

$app->run();
