<?php
/**
 * Created by PhpStorm.
 * User: dawen
 * Date: 11.11.13
 * Time: 13:19
 */

namespace Dawen\Bundle\PhpRedisBundle\Component;

class RedisClient implements RedisClientInterface
{
    /** @var \Redis  */
    private $redis;


    public function __construct(\Redis $redis)
    {
        $this->redis = $redis;
    }

    public function __destruct()
    {
        $this->redis->close();
    }

    /**
     * STRINGS
     * *************************************************************************************************
     */

    /**
     * Get the value related to the specified key
     *
     * @param string $key
     * @return bool|string
     */
    public function get($key)
    {
        return $this->redis->get($key);
    }

    /**
     * Set the string value in argument as value of the key.
     *
     * @param string $key
     * @param mixed $value
     * @param int $timeout
     * @return bool
     */
    public function set($key, $value, $timeout = 0)
    {
        return $this->redis->set($key, $value, (int)$timeout);
    }




}