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
        $this->redis->close();
    }

    /**
     * KEYS
     * *************************************************************************************************
     */

    /**
     * Verify if the specified key exists.
     *
     * @param   string $key
     * @return  bool: If the key exists, return TRUE, otherwise return FALSE.
     * @link    http://redis.io/commands/exists
     * @example
     * <pre>
     * $redis->set('key', 'value');
     * $redis->exists('key');               //  TRUE
     * $redis->exists('NonExistingKey');    // FALSE
     * </pre>
     */
    public function exists($key)
    {
        return $this->redis->exists($key);
    }

    /**
     * Returns the keys that match a certain pattern.
     *
     * @param   string  $pattern pattern, using '*' as a wildcard.
     * @return  array
     */
    public function keys($pattern)
    {
        return $this->redis->keys($pattern);
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
        return $this->redis->flushDB();
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

    /**
     * Set the string value in argument as value of the key, with a time to live.
     *
     * @param   string  $key
     * @param   int     $ttl
     * @param   string  $value
     * @return  bool:   TRUE if the command is successful.
     * @link    http://redis.io/commands/setex
     * @example $redis->setex('key', 3600, 'value'); // sets key → value, with 1h TTL.
     */
    public function setex($key, $ttl, $value)
    {
        return $this->redis->setex($key, $ttl, $value);
    }

    /**
     * Set the string value in argument as value of the key if the key doesn't already exist in the database.
     *
     * @param   string  $key
     * @param   string  $value
     * @return  bool:   TRUE in case of success, FALSE in case of failure.
     * @link    http://redis.io/commands/setnx
     * @example
     * <pre>
     * $redis->setnx('key', 'value');   // return TRUE
     * $redis->setnx('key', 'value');   // return FALSE
     * </pre>
     */
    public function setnx($key, $value)
    {
        return $this->redis->setnx($key, $value);
    }



}