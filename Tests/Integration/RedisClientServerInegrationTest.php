<?php
/**
 * Created by PhpStorm.
 * User: dawen
 * Date: 03.12.13
 * Time: 08:09
 */

namespace Dawen\Bundle\PhpRedisBundle\Tests\Integration;

use Dawen\Bundle\PhpRedisBundle\Component\RedisClient;
use Dawen\Bundle\PhpRedisBundle\Component\RedisClientInterface;
use Dawen\Bundle\PhpRedisBundle\Tests\Fixtures\AbstractKernelAwareTest;

class RedisClientServerIntegrationTest extends AbstractKernelAwareTest
{

    /** @var RedisClientInterface */
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
                if(!$connected) {
                    $this->skipped = true;
                    $this->markTestSkipped('could not connect to server');
                }
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

    public function testKeysFlushDB()
    {
        $keys = $this->client->keys('*');
        $this->assertEmpty($keys);

        $this->client->set('test', 'test');

        $keys = $this->client->keys('*');
        $this->assertCount(1, $keys);

        $success = $this->client->flushDB();
        $this->assertTrue($success);

        $keys = $this->client->keys('*');
        $this->assertEmpty($keys);
    }

    public function testConfigGet()
    {
        $key = '*';

        $resultConfigGet = $this->client->config(RedisClientInterface::CONFIG_OPERATION_GET, $key);
        $this->assertLessThan(count($resultConfigGet),20);

    }

    public function testConfigSet()
    {
        $key = 'maxmemory-samples';
        $newValue = 5;

        $resultConfigGet = $this->client->config(RedisClientInterface::CONFIG_OPERATION_GET, $key);

        $resultSetNew = $this->client->config(RedisClientInterface::CONFIG_OPERATION_SET, $key, $newValue);
        $this->assertTrue($resultSetNew);

        $resultConfigAfterSet = $this->client->config(RedisClientInterface::CONFIG_OPERATION_GET, $key);
        $this->assertEquals(5, $resultConfigAfterSet[$key]);

        $resultSetOld = $this->client->config(RedisClientInterface::CONFIG_OPERATION_SET, $key, $resultConfigGet[$key]);
        $this->assertTrue($resultSetOld);

        $resultConfigLastGet = $this->client->config(RedisClientInterface::CONFIG_OPERATION_GET, $key);
        $this->assertEquals(3, $resultConfigLastGet[$key]);
    }



}