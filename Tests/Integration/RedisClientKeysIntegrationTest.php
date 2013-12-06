<?php
/**
 * Created by PhpStorm.
 * User: dawen
 * Date: 03.12.13
 * Time: 08:09
 */

namespace Dawen\Bundle\PhpRedisBundle\Tests\Integration;

use Dawen\Bundle\PhpRedisBundle\Component\RedisClient;
use Dawen\Bundle\PhpRedisBundle\Tests\Fixtures\AbstractKernelAwareTest;

class RedisClientKeysIntegrationTest extends AbstractKernelAwareTest
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

    public function testKeysEmpty()
    {
        $keys = $this->client->keys('*');
        $this->assertEmpty($keys);
    }

    public function testDelOneValue()
    {
        $key1 = 'key1';
        $val = 'val';
        $set = $this->client->set($key1, $val);
        $this->assertTrue($set);
        $keys = $this->client->keys('key*');
        $this->assertCount(1, $keys);

        //delete test
        $deleted = $this->client->del($key1);
        $this->assertEquals(1, $deleted);
        $keys = $this->client->keys('key*');
        $this->assertCount(0, $keys);
    }

    public function testDelThreeValue()
    {
        $key1 = 'key1';
        $key2 = 'key2';
        $key3 = 'key3';
        $key4 = 'key4';
        $val = 'val';
        $set1 = $this->client->set($key1, $val);
        $set2 = $this->client->set($key2, $val);
        $set3 = $this->client->set($key3, $val);
        $set4 = $this->client->set($key4, $val);
        $this->assertTrue($set1);
        $this->assertTrue($set2);
        $this->assertTrue($set3);
        $this->assertTrue($set4);
        $keys = $this->client->keys('key*');
        $this->assertCount(4, $keys);

        //delete test
        $deleted = $this->client->del($key1, $key2, $key3);
        $this->assertEquals(3, $deleted);
        $keys = $this->client->keys('key*');
        $this->assertCount(1, $keys);
        $this->assertTrue(in_array($key4, $keys));
    }

    public function testDelArray()
    {
        $key1 = 'key1';
        $key2 = 'key2';
        $key3 = 'key3';
        $key4 = 'key4';
        $val = 'val';
        $set1 = $this->client->set($key1, $val);
        $set2 = $this->client->set($key2, $val);
        $set3 = $this->client->set($key3, $val);
        $set4 = $this->client->set($key4, $val);
        $this->assertTrue($set1);
        $this->assertTrue($set2);
        $this->assertTrue($set3);
        $this->assertTrue($set4);
        $keys = $this->client->keys('key*');
        $this->assertCount(4, $keys);

        //delete test
        $deleted = $this->client->del(array($key1, $key2, $key3, $key4));
        $this->assertEquals(4, $deleted);
        $keys = $this->client->keys('key*');
        $this->assertCount(0, $keys);
    }

    public function testExists()
    {

        $key = 'testKey';

        $extists = $this->client->exists($key);
        $this->assertFalse($extists);

        $this->client->set($key, array('test1', 'test2'));

        $extists = $this->client->exists($key);
        $this->assertTrue($extists);
    }

    public function testKeysAll()
    {
        $this->client->set('key1', array('test1', 'test2'));
        $this->client->set('key2', 'test');

        $keys = $this->client->keys('*');
        $this->assertCount(2, $keys);
        $this->assertTrue(in_array('key1', $keys));
        $this->assertTrue(in_array('key2', $keys));
    }

    public function testKeysSpecial()
    {
        $this->client->set('key1', array('test1', 'test2'));
        $this->client->set('key2', 'test');
        $this->client->set('key10', 'test');

        $keys = $this->client->keys('key*');
        $this->assertCount(3, $keys);
        $this->assertTrue(in_array('key10', $keys));
        $this->assertTrue(in_array('key2', $keys));
        $this->assertTrue(in_array('key1', $keys));

        $this->client->set('mytest.10.test', 'test');
        $keys = $this->client->keys('mytest.*.test');
        $this->assertCount(1, $keys);
        $this->assertTrue(in_array('mytest.10.test', $keys));
    }

}