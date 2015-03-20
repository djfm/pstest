<?php

require_once implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', '..', 'vendor', 'autoload.php']);

$serverAddress = $argv[1];

$worker = new PrestaShop\TestRunner\Worker;

$worker->setServerAddress($serverAddress);

$worker->run();
