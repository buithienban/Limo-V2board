<?php

require_once __DIR__ . '/vendor/autoload.php';

use Adapterman\Adapterman;
use Workerman\Worker;

Adapterman::init();

$ncpu = substr_count((string)@file_get_contents('/proc/cpuinfo'), "\nprocessor")+1;

$http_worker                = new Worker('http://127.0.0.1:6600');
$http_worker->count         = $ncpu * 2;
$http_worker->name          = 'AdapterMan';

$http_worker->onWorkerStart = static function () {
    //init();
    require __DIR__.'/start.php';
};

$http_worker->onMessage = static function ($connection, $request) {

    $connection->send(run());
};

Worker::runAll();
