<?php
/**
 * Created by PhpStorm.
 * User: dawen
 * Date: 14.11.13
 * Time: 22:03
 */

namespace Dawen\Bundle\PhpRedisBundle\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RedisDataCollector extends DataCollector implements RedisDataCollectorInterface
{

    public function __construct(array $configs = array())
    {
        $this->data = array('commands' => array()
                            , 'configs' => $configs);

    }

    /**
     * adds data to collect
     *
     * @param array $data
     * @return void
     */
    public function add(array $data)
    {
        $this->data['commands'][] = $data;
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        //do nothing
    }

    /**
     * get configurations
     *
     * @return array
     */
    public function getConfigs()
    {

        if(isset($this->data['configs'][0]['clients']))
        {
            return $this->data['configs'][0]['clients'];
        }
        return array();

    }

    /**
     * returns the number of entries in data
     *
     * @return int
     */
    public function getCount()
    {
        return count($this->data['commands']);
    }

    /**
     * gets all collected data (commands)
     *
     * @return array
     */
    public function getData()
    {
        return $this->data['commands'];
    }

    /**
     * gets taken time of all commands
     *
     * @return float
     */
    public function getTimeTaken()
    {
        $timeTaken = 0;
        foreach($this->data['commands'] as $command)
        {
            $timeTaken += $command['time_taken'];
        }

        return $timeTaken;
    }

    /**
     * @return string|void
     */
    public function getName()
    {
        return 'redis';
    }

}