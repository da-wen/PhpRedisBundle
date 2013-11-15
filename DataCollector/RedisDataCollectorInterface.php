<?php
/**
 * Created by PhpStorm.
 * User: dawen
 * Date: 14.11.13
 * Time: 22:40
 */

namespace Dawen\Bundle\PhpRedisBundle\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;

interface RedisDataCollectorInterface extends DataCollectorInterface
{
    /**
     * adds data to collect
     *
     * @param array $data
     * @return void
     */
    public function add(array $data);

    /**
     * get configurations
     *
     * @return array
     */
    public function getConfigs();

    /**
     * returns the number of entries in data
     *
     * @return int
     */
    public function getCount();

    /**
     * gets all collected data (commands)
     *
     * @return array
     */
    public function getData();

    /**
     * gets taken time of all commands
     *
     * @return float
     */
    public function getTimeTaken();

}