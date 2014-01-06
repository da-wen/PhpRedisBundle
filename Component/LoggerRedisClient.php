<?php
/**
 * Created by PhpStorm.
 * User: dawen
 * Date: 14.11.13
 * Time: 08:09
 */

namespace Dawen\Bundle\PhpRedisBundle\Component;

use Dawen\Bundle\PhpRedisBundle\DataCollector\RedisDataCollectorInterface;
use Psr\Log\LoggerInterface;

class LoggerRedisClient implements RedisClientInterface
{
    /** @var RedisClientInterface  */
    private $redis;

    /** @var LoggerInterface */
    private $logger;

    /** @var  array */
    private $config;

    /** @var RedisDataCollectorInterface  */
    private $collector;

    /**
     * @param RedisClientInterface $redis
     * @param LoggerInterface $logger
     * @param RedisDataCollectorInterface $collector
     * @param array $config
     */
    public function __construct(RedisClientInterface $redis,
                                LoggerInterface $logger,
                                RedisDataCollectorInterface $collector,
                                array $config)
    {
        $this->redis = $redis;
        $this->logger = $logger;
        $this->config = $config;
        $this->collector = $collector;
    }

    /**
     * phpredis functionality
     * ***************************************************************************************************************
     */

    /**
     * CONNECTION
     * *************************************************************************************************
     */

    /**
     * Disconnects from the Redis instance, except when pconnect is used.
     *
     * @return void
     */
    public function close()
    {
        $startTime = $this->startMeasure();
        $this->redis->close();
        $duration = $this->endMeasure($startTime);

        $params = array();
        $this->info('close', $duration, $params);
    }

    /**
     * @inheritdoc
     */
    public function select($dbindex)
    {
        $startTime = $this->startMeasure();
        $result = $this->redis->select($dbindex);
        $duration = $this->endMeasure($startTime);

        $params = array('dbindex' => $dbindex);
        if(false === $result)
        {
            $this->warning('select', $duration, $params);
        }
        else
        {
            $this->info('select', $duration, $params);
        }

        return $result;
    }

    /**
     * KEYS
     * *************************************************************************************************
     */

    /**
     * @inheritdoc
     */
    public function del($key1, $key2 = null,$key3 = null)
    {
        $startTime = $this->startMeasure();
        $result = $this->redis->del($key1, $key2, $key3);
        $duration = $this->endMeasure($startTime);

        $params = array('key1' => $key1
                        , 'key2' => $key2
                        , 'key3' => $key3
                        , 'result' => $result);

        $this->info('del', $duration, $params);

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function dump($key)
    {
        $startTime = $this->startMeasure();
        $result = $this->redis->dump($key);
        $duration = $this->endMeasure($startTime);

        $params = array('key' => $key);
        if(false === $result)
        {
            $this->warning('dump', $duration, $params);
        }
        else
        {
            $this->info('dump', $duration, $params);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function exists($key)
    {
        $startTime = $this->startMeasure();
        $success = $this->redis->exists($key);
        $duration = $this->endMeasure($startTime);

        $params = array('key' => $key);

        $this->info('exists', $duration, $params);

        return $success;
    }

    /**
     * @inheritdoc
     */
    public function expire($key, $ttl)
    {
        $startTime = $this->startMeasure();
        $result = $this->redis->expire($key, $ttl);
        $duration = $this->endMeasure($startTime);

        $params = array('key' => $key
                        , 'ttl' => $ttl);

        if(false === $result)
        {
            $this->warning('expire', $duration, $params);
        }
        else
        {
            $this->info('expire', $duration, $params);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function expireAt($key, $timestamp)
    {
        $startTime = $this->startMeasure();
        $result = $this->redis->expireAt($key, $timestamp);
        $duration = $this->endMeasure($startTime);

        $params = array('key' => $key
                        , 'timestamp' => $timestamp);

        if(false === $result)
        {
            $this->warning('expire', $duration, $params);
        }
        else
        {
            $this->info('expire', $duration, $params);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function keys($pattern)
    {
        $startTime = $this->startMeasure();
        $strings = $this->redis->keys($pattern);
        $duration = $this->endMeasure($startTime);

        $params = array('pattern' => $pattern);
        $this->info('keys', $duration, $params);

        return $strings;
    }

    /**
     * @inheritdoc
     */
    public function migrate($host, $port, $key, $db, $timeout)
    {
        $startTime = $this->startMeasure();
        $result = $this->redis->migrate($host, $port, $key, $db, $timeout);
        $duration = $this->endMeasure($startTime);

        $params = array('host' => $host
                        , 'port' => $port
                        , 'key' => $key
                        , 'db' => $db
                        , 'timeout' => $timeout);

        if(false === $result)
        {
            $this->warning('migrate', $duration, $params);
        }
        else
        {
            $this->info('migrate', $duration, $params);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function move($key, $dbindex)
    {
        $startTime = $this->startMeasure();
        $result = $this->redis->move($key, $dbindex);
        $duration = $this->endMeasure($startTime);

        $params = array('key' => $key
                        , 'dbindex' => $dbindex);

        if(false === $result)
        {
            $this->warning('move', $duration, $params);
        }
        else
        {
            $this->info('move', $duration, $params);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function object($string, $key)
    {

        $startTime = $this->startMeasure();
        $result = $this->redis->object($string, $key);
        $duration = $this->endMeasure($startTime);

        $params = array('key' => $key
                        , 'string' => $string);

        if(false === $result)
        {
            $this->warning('object', $duration, $params);
        }
        else
        {
            $this->info('object', $duration, $params);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function persist($key)
    {
        $startTime = $this->startMeasure();
        $result = $this->redis->persist($key);
        $duration = $this->endMeasure($startTime);

        $params = array('key' => $key);

        if(false === $result)
        {
            $this->warning('persist', $duration, $params);
        }
        else
        {
            $this->info('persist', $duration, $params);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function randomKey()
    {
        $startTime = $this->startMeasure();
        $result = $this->redis->randomKey();
        $duration = $this->endMeasure($startTime);

        $params = array();

        if(false === $result)
        {
            $this->warning('randomKey', $duration, $params);
        }
        else
        {
            $this->info('randomKey', $duration, $params);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function rename($srcKey, $dstKey)
    {
        $startTime = $this->startMeasure();
        $result = $this->redis->rename($srcKey, $dstKey);
        $duration = $this->endMeasure($startTime);

        $params = array('srcKey' => $srcKey
                        , 'dstKey' => $dstKey);

        if(false === $result)
        {
            $this->warning('rename', $duration, $params);
        }
        else
        {
            $this->info('rename', $duration, $params);
        }

        return $result;
    }


    /**
     * SERVER
     * *************************************************************************************************
     */

    /**
     * Removes all entries from the current database.
     *
     * @return  bool: Always TRUE.
     * @link    http://redis.io/commands/flushdb
     * @example $redis->flushDB();
     */
    public function flushDB()
    {
        $startTime = $this->startMeasure();
        $success = $this->redis->flushDB();;
        $duration = $this->endMeasure($startTime);

        $params = array();
        if($success)
        {
            $this->info('flushDB', $duration, $params);
        }
        else
        {
            $this->error('flushDB', $duration, $params);
        }

        return $success;
    }

    /**
     * STRINGS
     * *************************************************************************************************
     */

    /**
     * @inheritdoc
     */
    public function append($key, $value)
    {
        $startTime = $this->startMeasure();
        $stringSize = $this->redis->append($key, $value);
        $duration = $this->endMeasure($startTime);

        $params = array('key' => $key, 'stringSize' => $stringSize);
        $this->info('append', $duration, $params);

        return $stringSize;
    }

    /**
     * @inheritdoc
     */
    public function bitCount($key)
    {
        $startTime = $this->startMeasure();
        $result = $this->redis->bitCount($key);
        $duration = $this->endMeasure($startTime);

        $params = array('key' => $key);
        $this->info('bitCount', $duration, $params);

        return $result;
    }

//    /**
//     * @inheritdoc
//     */
//    public function bitOp($operation, $retKey, $key1, $key2, $key3 = null)
//    {
//        $startTime = $this->startMeasure();
//        $result = $this->redis->bitOp($operation, $retKey, $key1, $key2, $key3);
//        $duration = $this->endMeasure($startTime);
//
//        $params = array('operation' => $operation
//                        , 'retKey' => $retKey
//                        , 'key1' => $key1
//                        , 'key2' => $key2);
//
//        if(null !== $key3) {
//            $params['key3'] = $key3;
//        }
//
//        $this->info('bitOp', $duration, $params);
//
//        return $result;
//    }

    /**
     * @inheritdoc
     */
    public function decr($key)
    {
        $startTime = $this->startMeasure();
        $result = $this->redis->decr($key);
        $duration = $this->endMeasure($startTime);

        $params = array('key' => $key);
        if(false === $result)
        {
            $this->warning('decr', $duration, $params);
        }
        else
        {
            $this->info('decr', $duration, $params);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function get($key)
    {
        $startTime = $this->startMeasure();
        $value = $this->redis->get($key);
        $duration = $this->endMeasure($startTime);

        $params = array('key' => $key);
        if(false === $value)
        {
            $this->warning('get', $duration, $params);
        }
        else
        {
            $this->info('get', $duration, $params);
        }

        return $value;
    }

    /**
     * @inheritdoc
     */
    public function getBit($key, $offset)
    {
        $startTime = $this->startMeasure();
        $result = $this->redis->getBit($key, $offset);
        $duration = $this->endMeasure($startTime);

        $params = array('key' => $key
                        , 'offset' => $offset);
        if(false === $result)
        {
            $this->warning('getBit', $duration, $params);
        }
        else
        {
            $this->info('getBit', $duration, $params);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getRange($key, $start, $end)
    {
        $startTime = $this->startMeasure();
        $result = $this->redis->getRange($key, $start, $end);
        $duration = $this->endMeasure($startTime);

        $params = array('key' => $key
                        , 'start' => $start
                        , 'end' => $end);
        if(false === $result)
        {
            $this->warning('getRange', $duration, $params);
        }
        else
        {
            $this->info('getRange', $duration, $params);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getSet($key, $value)
    {
        $startTime = $this->startMeasure();
        $result = $this->redis->getSet($key, $value);
        $duration = $this->endMeasure($startTime);

        $params = array('key' => $key);
        if(false === $result)
        {
            $this->warning('getSet', $duration, $params);
        }
        else
        {
            $this->info('getSet', $duration, $params);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function incr($key)
    {
        $startTime = $this->startMeasure();
        $result = $this->redis->incr($key);
        $duration = $this->endMeasure($startTime);

        $params = array('key' => $key);
        if(false === $result)
        {
            $this->warning('incr', $duration, $params);
        }
        else
        {
            $this->info('incr', $duration, $params);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function incrByFloat($key, $increment)
    {
        $startTime = $this->startMeasure();
        $result = $this->redis->incrByFloat($key, $increment);
        $duration = $this->endMeasure($startTime);

        $params = array('key' => $key
                        , 'increment' => $increment);

        if(false === $result)
        {
            $this->warning('incrByFloat', $duration, $params);
        }
        else
        {
            $this->info('incrByFLoat', $duration, $params);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function mget(array $keys)
    {
        $startTime = $this->startMeasure();
        $result = $this->redis->mget($keys);
        $duration = $this->endMeasure($startTime);

        $params = array('keys' => $keys);
        if(false === $result)
        {
            $this->warning('mget', $duration, $params);
        }
        else
        {
            $this->info('mget', $duration, $params);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function mset(array $array)
    {
        $startTime = $this->startMeasure();
        $result = $this->redis->mset($array);
        $duration = $this->endMeasure($startTime);

        $params = array('keys' => array_keys($array));
        if(false === $result)
        {
            $this->warning('mset', $duration, $params);
        }
        else
        {
            $this->info('mset', $duration, $params);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function set($key, $value, $timeout = 0)
    {
        $startTime = $this->startMeasure();
        $success = $this->redis->set($key, $value, (int)$timeout);
        $duration = $this->endMeasure($startTime);

        $params = array('key' => $key, 'timeout' => (int)$timeout);
        if($success)
        {
            $this->info('set', $duration, $params);
        }
        else
        {
            $this->error('set', $duration, $params);
        }


        return $success;
    }

    /**
     * @inheritdoc
     */
    public function setBit($key, $offset, $value)
    {
        $startTime = $this->startMeasure();
        $success = $this->redis->setBit($key, $value, $value);
        $duration = $this->endMeasure($startTime);

        $params = array('key' => $key
                        , 'offset' => $offset);
        if($success)
        {
            $this->info('setBit', $duration, $params);
        }
        else
        {
            $this->error('setBit', $duration, $params);
        }


        return $success;
    }

    /**
     * @inheritdoc
     */
    public function setex($key, $ttl, $value)
    {
        $startTime = $this->startMeasure();
        $success = $this->redis->setex($key, (int)$ttl, $value);
        $duration = $this->endMeasure($startTime);


        $params = array('key' => $key, 'ttl' => (int)$ttl);
        if($success)
        {
            $this->info('setex', $duration, $params);
        }
        else
        {
            $this->error('setex', $duration, $params);
        }

        return $success;
    }

    /**
     * @inheritdoc
     */
    public function setnx($key, $value)
    {
        $startTime = $this->startMeasure();
        $success = $this->redis->setnx($key, $value);
        $duration = $this->endMeasure($startTime);

        $params = array('key' => $key);
        $params['notExistingKey'] = $success;

        $this->info('setnx', $duration, $params);

        return $success;
    }

    /**
     * @inheritdoc
     */
    public function setRange($key, $offset, $value)
    {
        $startTime = $this->startMeasure();
        $result = $this->redis->setRange($key, $offset, $value);
        $duration = $this->endMeasure($startTime);


        $params = array('key' => $key, 'offset' => $offset);
        if($result)
        {
            $this->info('setRange', $duration, $params);
        }
        else
        {
            $this->error('setRange', $duration, $params);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function strlen($key)
    {
        $startTime = $this->startMeasure();
        $result = $this->redis->strlen($key);
        $duration = $this->endMeasure($startTime);

        $params = array('key' => $key);
        if($result)
        {
            $this->info('strlen', $duration, $params);
        }
        else
        {
            $this->error('strlen', $duration, $params);
        }

        return $result;

    }


    /**
     * Logger functionality.
     * ***************************************************************************************************************
     */

    /**
     * gets the collector/commands
     *
     * @return RedisDataCollectorInterface
     */
    public function getCommands()
    {
        return $this->collector;
    }


    /**
     * count executed commands
     *
     * @return int
     */
    public function countCommand()
    {
        return count($this->collector);
    }

    /**
     * add commands to the collector
     *
     * @param string $method
     * @param float $timeTaken
     * @param string $logType
     * @param array $params
     */
    private function collect($method, $timeTaken, $logType, array $params)
    {
        $this->collector->add(array(
            'cmd' => $method,
            'time_taken' => $timeTaken,
            'config' => $this->config,
            'log_type' => $logType,
            'params' => $params

        ));

    }

    /**
     * logs with info level
     *
     * @param string $method
     * @param float $timeTaken
     * @param array $params
     */
    private function info($method, $timeTaken, array $params)
    {
        //add data to data collector
        $this->collect($method, $timeTaken, 'info', $params);

        //prepare for logger
        $host = $this->config['host'];
        $host.= (isset($this->config['port'])) ? ':' . $this->config['port'] : '' ;
        //log
        $this->logger->info('Command: "' . $method . '" Host: '.$host.' DB: "'.$this->config['db'] . '"', $params);
    }

    /**
     * logs with error level
     *
     * @param string $method
     * @param float $timeTaken
     * @param array $params
     */
    private function error($method, $timeTaken, array $params)
    {
        //add data to data collector
        $this->collect($method, $timeTaken, 'error', $params);

        //prepare for logger
        $host = $this->config['host'];
        $host.= (isset($this->config['port'])) ? ':' . $this->config['port'] : '' ;
        //log
        $this->logger->error('Command: "' . $method . '" Host: '.$host.' DB: "'.$this->config['db'] . '"', $params);
    }

    /**
     * logs with waring level
     *
     * @param string $method
     * @param float $timeTaken
     * @param array $params
     */
    private function warning($method, $timeTaken, array $params)
    {
        //add data to data collector
        $this->collect($method, $timeTaken, 'warning', $params);

        //prepare for logger
        $host = $this->config['host'];
        $host.= (isset($this->config['port'])) ? ':' . $this->config['port'] : '' ;
        //log
        $this->logger->warning('Command: "' . $method . '" Host: '.$host.' DB: "'.$this->config['db'] . '"', $params);
    }

    /**
     * returns a microtime
     *
     * @return float
     */
    private function startMeasure()
    {
        return microtime(true);
    }

    /**
     * returns a formatted time as float, calculated to ms
     *
     * @param float $timeStart
     * @return float
     */
    private function endMeasure($timeStart)
    {
        return (microtime(true) - $timeStart) * 1000;
    }
}