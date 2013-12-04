<?php
/**
 * Created by PhpStorm.
 * User: dawen
 * Date: 11.11.13
 * Time: 13:31
 */
namespace Dawen\Bundle\PhpRedisBundle\Component;


interface RedisClientInterface
{

    /**
     * CONNECTION
     * *************************************************************************************************
     */

    /**
     * Disconnects from the Redis instance, except when pconnect is used.
     *
     * @return void
     */
    public function close();

    /**
     * KEYS
     * *************************************************************************************************
     */

    public function keys($pattern);

    /**
     * SERVER
     * *************************************************************************************************
     */
    public function flushDB();

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
    public function get($key);

    /**
     * Set the string value in argument as value of the key.
     *
     * @param string $key
     * @param mixed $value
     * @param int $timeout
     * @return bool
     */
    public function set($key, $value, $timeout = 0);

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
    public function setex($key, $ttl, $value);

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
    public function setnx($key, $value);
}