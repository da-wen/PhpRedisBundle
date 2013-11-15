<?php

namespace Dawen\Bundle\PhpRedisBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Definition;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class PhpRedisExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        if(isset($config['clients']))
        {
            foreach($config['clients'] as $connectionName => $client)
            {
                $this->loadClient($connectionName, $client, $container);
            }
        }

        if($container->hasDefinition('php_redis.data_collector'))
        {
            $dataCollectorDef = $container->getDefinition('php_redis.data_collector');
            $dataCollectorDef->addArgument($config);
        }
    }

    private function loadClient($connectionName, array $client, ContainerBuilder $container)
    {
        //check for connect or pconnect
        $connectMethod = $client['pconnect'] ? 'pconnect' : 'connect';

        //build connection params
        $connectParams = array($client['host']);
        if(false === strpos($client['host'], '.sock'))
        {
            $connectParams[] = $client['port'];
            if(is_float($client['connection_timeout']) || is_int($client['connection_timeout']))
            {
                $connectParams[] = $client['connection_timeout'];
            }
        }

        $redisId = sprintf('php_redis.connection.%s', $connectionName);
        $redisDef = new Definition($container->getParameter('php_redis.redis.class'));
        $redisDef->addMethodCall($connectMethod, $connectParams);
        $redisDef->addMethodCall('select', array($client['db']));
        $redisDef->setPublic(false);
        //set redis connection
        $container->setDefinition($redisId, $redisDef);

        $clientId = sprintf('php_redis.client.%s', $connectionName);
        $clientDef = new Definition($container->getParameter('php_redis.redisclient.class'));
        $clientDef->setPublic(false);
        $clientDef->addArgument(new Reference($redisId));
        //set client
        $container->setDefinition($clientId, $clientDef);

        //set alias for client
        $aliasId = sprintf('php_redis.%s', $connectionName);

        if($client['logging'])
        {
            $client['connection_name'] = $connectionName;
            $loggerId = sprintf('php_redis.logger.client.%s', $connectionName);
            $loggerDef = new Definition($container->getParameter('php_redis.loggerredisclient.class'));
            $loggerDef->setPublic(false);
            $loggerDef->addArgument(new Reference($clientId));
            $loggerDef->addArgument(new Reference('logger'));
            $loggerDef->addArgument(new Reference('php_redis.data_collector'));
            $loggerDef->addArgument($client);
            $loggerDef->addTag('monolog.logger',array('channel' => 'php_redis'));
            //set logger client
            $container->setDefinition($loggerId, $loggerDef);

            $container->setAlias($aliasId, $loggerId);
        }
        else
        {
            $container->setAlias($aliasId, $clientId);
        }


    }
}
