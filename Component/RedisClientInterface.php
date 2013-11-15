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
}