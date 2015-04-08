<?php

@ini_set('display_errors', 'on');

function exception_error_handler($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
}
set_error_handler("exception_error_handler");

require_once implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', '..', 'vendor', 'autoload.php']);

$serverAddress = $argv[1];

$worker = new PrestaShop\TestRunner\Worker;

$worker->setServerAddress($serverAddress);

$worker->run();
