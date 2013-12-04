<?php
/**
 * Created by PhpStorm.
 * User: dawen
 * Date: 04.12.13
 * Time: 18:30
 */

namespace Dawen\Bundle\PhpRedisBundle\Tests\Integration;

use Dawen\Bundle\PhpRedisBundle\Component\RedisClient;
use Dawen\Bundle\PhpRedisBundle\Tests\Fixtures\AbstractKernelAwareTest;

class RedisClientStringsIntegrationTest extends AbstractKernelAwareTest
{

    /** @var RedisClient */
    private $client;

    private $skipped = false;

    public function setUp()
    {
        parent::setUp();

        if($this->container->hasParameter('redis'))
        {
            $redisParams = $this->container->getParameter('redis');
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
        $this->skipped = false;
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Dawen\Bundle\PhpRedisBundle\Component\RedisClientInterface', $this->client);
    }

    public function testSetGet()
    {
        $key = 'testKey';
        $value = 'testValue';

        $success = $this->client->set($key, $value);
        $this->assertTrue($success);

        $fetchedValue = $this->client->get($key);
        $this->assertSame($value, $fetchedValue);
    }

    public function testSetWithTtl()
    {
        $key = 'testKey';
        $value = 'testValue';
        $ttl = 1;

        $success = $this->client->set($key, $value, $ttl);
        $this->assertTrue($success);
        sleep($ttl + 1);
        $fetchedValue = $this->client->get($key);
        $this->assertFalse($fetchedValue);
    }

    public function testSetEx()
    {
        $key = 'testKey';
        $value = 'testValue';
        $ttl = 1;

        $success = $this->client->setex($key, $ttl, $value);
        $this->assertTrue($success);
        sleep($ttl + 1);
        $fetchedValue = $this->client->get($key);
        $this->assertFalse($fetchedValue);
    }

    public function testSetNx()
    {
        $key = 'testKey';
        $value = 'testValue';

        $success = $this->client->setnx($key, $value);
        $this->assertTrue($success);

        $successExtisting = $this->client->setnx($key, $value);
        $this->assertFalse($successExtisting);
    }


}