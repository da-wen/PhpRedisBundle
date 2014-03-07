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

    private $params;

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

                $this->params = $redisParams;

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

        $this->params = null;
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

    public function testBgrewriteaof()
    {
        $result = $this->client->bgrewriteaof();
        $this->assertTrue($result);
    }

//    public function testBgsave()
//    {
//        $result = $this->client->bgsave();
//        $this->assertFalse($result);
//    }

    public function testFlushAll()
    {
        $this->client->set('myKey', 'myVal');
        $this->assertCount(1, $this->client->keys('*'));

        $this->client->select($this->params['db2']);

        $this->client->set('myKey', 'myVal');
        $this->assertCount(1, $this->client->keys('*'));

        $result = $this->client->flushAll();
        $this->assertTrue($result);

        $this->assertCount(0, $this->client->keys('*'));

        $this->client->select($this->params['db']);

        $this->assertCount(0, $this->client->keys('*'));

    }

    public function testDbSize()
    {
        $this->assertEquals(0, $this->client->dbSize());
        $this->client->set('myKey', 'myVal');
        $this->assertEquals(1, $this->client->dbSize());
    }

    public function testInfo()
    {
        $result = $this->client->info();

        $this->assertContains('process_id', $result);
        $this->assertContains('redis_version', $result);


    }

    public function testInfoOption()
    {
        $result = $this->client->info('CPU');

        $this->assertNotContains('process_id', $result);
        $this->assertNotContains('redis_version', $result);
        $this->assertTrue(isset($result['used_cpu_sys']));
        $this->assertTrue(isset($result['used_cpu_user']));
    }

    public function testLastSave()
    {
        $this->assertGreaterThan(100000, $this->client->lastSave());
    }

    public function testSave()
    {
        $this->assertTrue($this->client->save());
    }

    public function testTime()
    {
        $this->assertCount(2, $this->client->time());
    }

}