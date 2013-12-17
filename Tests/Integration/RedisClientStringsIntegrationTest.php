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

    public function testAppend()
    {
        $key = 'testKey';
        $value1 = 'testValue1';
        $value2 = 'testValue2';

        $successSet = $this->client->set($key, $value1);
        $this->assertTrue($successSet);

        $successAppend = $this->client->append($key, $value2);
        $this->assertEquals(strlen($value1.$value2), $successAppend);

        $result = $this->client->get($key);
        $this->assertEquals($value1.$value2, $result);
    }

    public function testBitCount()
    {
        $key = 'testKey';
        $value = 'testValue';

        $successSet = $this->client->set($key, $value);
        $this->assertTrue($successSet);

        $result = $this->client->bitCount($key);
        $this->assertEquals(37, $result);
    }

//    public function testBitOp()
//    {
//        $key1 = 'testKey1';
//        $value1 = '123';
//        $key2 = 'testKey2';
//        $value2 = '234';
//
//        $successSet1 = $this->client->set($key1, $value1);
//        $successSet2 = $this->client->set($key2, $value2);
//        $this->assertTrue($successSet1);
//        $this->assertTrue($successSet2);
//
//        $retKey = 'testKey';
//
//        $result = $this->client->bitCount('XOR', $retKey, $key1, $key2);
//        $this->assertTrue($result > 0);
//    }

    public function testDecrNoKey()
    {
        $key = 'testKey';

        $resultNoKey = $this->client->decr($key);
        $this->assertEquals(-1, $resultNoKey);
    }

    public function testDecrKeySet()
    {
        $key = 'testKey';
        $value = 12;

        $success = $this->client->set($key, $value);
        $this->assertTrue($success);

        $result = $this->client->decr($key);
        $this->assertEquals(11, $result);
    }

    public function testDecrString()
    {
        $key = 'testKey';
        $value = 'hello';

        $success = $this->client->set($key, $value);
        $this->assertTrue($success);

        $result = $this->client->decr($key);
        $this->assertFalse($result);
    }

    public function testGetBit()
    {
        $key = 'testKey';
        $value = '\x7f';

        $success = $this->client->set($key, $value);
        $this->assertTrue($success);

        $result0 = $this->client->getBit($key, 0);
        $this->assertEquals(0, $result0);

        $result1 = $this->client->getBit($key, 1);
        $this->assertEquals(1, $result1);
    }

    public function testGetRange()
    {
        $key = 'testKey';
        $value = 'my test string';

        $success = $this->client->set($key, $value);
        $this->assertTrue($success);

        $resultMy = $this->client->getRange($key, 0, 1);
        $this->assertEquals('my', $resultMy);

        $resultTest = $this->client->getRange($key, 3, 6);
        $this->assertEquals('test', $resultTest);

        $resultString = $this->client->getRange($key, 8, 13);
        $this->assertEquals('string', $resultString);
    }


}