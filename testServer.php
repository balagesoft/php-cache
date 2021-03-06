<?php
ini_set('log_errors', 1);
ini_set('error_log', '/var/log/php-cache.log');
include_once 'vendor/autoload.php';

use PhpCache\CacheServer\CacheServer;
use PhpCache\ServiceManager\ServiceManager;

$config = require_once 'config.php';
$serviceManager = new ServiceManager($config);
$server = $serviceManager->get(CacheServer::class);
$server->run();