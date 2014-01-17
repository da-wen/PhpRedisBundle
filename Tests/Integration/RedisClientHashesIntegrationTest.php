<?php
/**
 * Created by PhpStorm.
 * User: dawen
 * Date: 17.01.14
 * Time: 08:10
 */

namespace Dawen\Bundle\PhpRedisBundle\Tests\Integration;

use Dawen\Bundle\PhpRedisBundle\Component\RedisClient;
use Dawen\Bundle\PhpRedisBundle\Tests\Fixtures\AbstractKernelAwareTest;

class RedisClientHashesIntegrationTest extends AbstractKernelAwareTest
{

    /** @var RedisClient */
    private $client;
    private $skipped = false;
    private $params;


    public function setUp()
    {
        parent::setUp();

        if($this->container->hasParameter('redis'))
        {
            $redisParams = $this->container->getParameter('redis');
            $this->params = $redisParams;
            if(!empty($redisParams['host']) && !empty($redisParams['port']))
            {
                $redis = new \Redis();
                $connected = $redis->pconnect($redisParams['host'], $redisParams['port']);
                $redis->select($redisParams['db']);

                $this->client = new RedisClient($redis);
            }
            else
            {
                $this->skipped = true;
                $this->markTestSkipped('parameter port and host must be set and filled');
            }
        }
        else
        {
            $this->skipped = true;
            $this->markTestSkipped('no parameters in config_test set');
        }
    }

    public function tearDown()
    {
        parent::tearDown();

        if(!$this->skipped)
        {
            $this->client->flushDB();
            $this->client->close();
        }

        $this->client = null;
        $this->params = null;
        $this->skipped = false;
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Dawen\Bundle\PhpRedisBundle\Component\RedisClientInterface', $this->client);
    }

    public function testHSetInsert()
    {
        $key = 'myTestKey';
        $hashKey = 'myHashKey';
        $value = 'test value';

        $resultSet = $this->client->hSet($key, $hashKey, $value);
        $this->assertEquals(1, $resultSet);

        $resultGet = $this->client->hGet($key, $hashKey);
        $this->assertEquals($value, $resultGet);
    }

    public function testHSetReplace()
    {
        $key = 'myTestKey';
        $hashKey = 'myHashKey';
        $value = 'test value';
        $valueReplace = 'new value';

        $resultSet = $this->client->hSet($key, $hashKey, $value);
        $this->assertEquals(1, $resultSet);

        $resultGet = $this->client->hGet($key, $hashKey);
        $this->assertEquals($value, $resultGet);

        $resultReplace = $this->client->hSet($key, $hashKey, $valueReplace);
        $this->assertEquals(0, $resultReplace);

        $resultReplace = $this->client->hGet($key, $hashKey);
        $this->assertEquals($valueReplace, $resultReplace);
    }

    public function testHGet()
    {
        $key = 'myTestKey';
        $hashKey = 'myHashKey';
        $value = 'test value';

        $resultSet = $this->client->hSet($key, $hashKey, $value);
        $this->assertEquals(1, $resultSet);

        $resultGet = $this->client->hGet($key, $hashKey);
        $this->assertEquals($value, $resultGet);

        $resultGet = $this->client->hGet($key, $hashKey);
        $this->assertEquals($value, $resultGet);

        $resultNoHashKey = $this->client->hGet($key, 'hashKeyMissing');
        $this->assertFalse($resultNoHashKey);
    }

}