<?php

namespace PhpCache\CacheServer;

/**
 * Description of ActionHandler
 *
 * @author dude920228
 */
class ActionHandler
{

    public function __invoke($data, $bucket, $ioHandler, $connection)
    {
        switch ($data['action']) {
            case 'set':
                $package = $data['message'];
                $bucket->store($data['key'], $package);
                $ioHandler->closeSocket($connection);
                break;
            case 'get':
                $key = $data['key'];
                $package = $bucket->get($key);
                $dataToSend = serialize($package);
                $ioHandler->writeToSocket($connection, $dataToSend);
                $ioHandler->closeSocket($connection);
                break;
            case 'delete':
                $key = $data['key'];
                $bucket->delete($key);
                $ioHandler->closeSocket($connection);
                break;
        }
    }
}
    