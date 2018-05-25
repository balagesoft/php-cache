<?php

/*
 * All rights reserved © 2018 Legow Hosting Kft.
 */

namespace PhpCache\ServiceManager;

use PhpMq\ServiceManager\Exception\NotFoundException;
use Psr\Container\ContainerInterface;

/**
 * Description of ServiceManager
 *
 * @author kdudas
 */
class ServiceManager implements ContainerInterface
{
    private $aliases;
    private $config;
    private $factories;
    private $invokables;
    
    public function __construct($config)
    {
        $this->configure($config);
    }
    
    private function configure($config)
    {
        if(isset($config['aliases'])) {
            $this->aliases = $config['aliases'];
        }
        if(isset($config['factories'])) {
            $this->factories = $config['factories'];
        }
        if(isset($config['invokables'])) {
            $this->invokables = $config['invokables'];
        }
    }
    
    public function get($id)
    {
        if($this->has($id)) {
            $serviceType = $this->getServiceType($id);
            if($serviceType == 'factory') {
                return (new $this->factories[$id])($this);
            }
            if($serviceType == 'invokable') {
                return new $this->invokables[$id];
            }
        }
        throw new NotFoundException(
            sprintf('Service "%s" couldn\'t be created! Reason: service not found', $id)
        );
    }

    private function getServiceType($id)
    {
        if(array_key_exists($id, $this->aliases)) {
            $serviceName = $this->getServiceForAlias($id);
            if(array_key_exists($serviceName, $this->factories)) {
                return 'factory';
            }
            else {
                return 'invokable';
            }
        }
        return array_key_exists($id, $this->factories) ? 'factory' : 'invokable';
    }
    
    private function getServiceForAlias($alias)
    {
        return array_key_exists($this->aliases[$alias], $this->factories) ?
                $this->factories[$this->aliases[$alias]] : $this->invokables[$this->aliases[$alias]];
    }
    
    public function has($id)
    {
        return array_key_exists($id, $this->aliases) ||
        array_key_exists($id, $this->factories) ||
        array_key_exists($id, $this->invokables);
    }

}