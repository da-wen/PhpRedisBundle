<?php
/**
 * Created by PhpStorm.
 * User: dawen
 * Date: 15.11.13
 * Time: 23:27
 */

namespace Dawen\Bundle\PhpRedisBundle\Tests\Component;

use Dawen\Bundle\PhpRedisBundle\Component\RedisClient;

class RedisClientStringsTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $redis = null;

    /** @var RedisClient */
    private $client = null;

    public function setUp()
    {
        if(!extension_loaded('redis'))
        {
            $this->markTestSkipped('no redis extension installed');
        }

        $this->redis = $this->getMockBuilder('Redis')
                            ->disableOriginalConstructor()
                            ->getMock();

        $this->client = new RedisClient($this->redis);

    }

    public function tearDown()
    {
        $this->redis = null;
        $this->client = null;
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Dawen\Bundle\PhpRedisBundle\Component\RedisClientInterface', $this->client);
    }

    public function testSet()
    {
        $testKey = 'myTestKey';
        $testValue = 'myTestValue';
        $testTimeout = 22;

        $this->redis->expects($this->once())
                    ->method('set')
                    ->with( $this->equalTo($testKey),
                            $this->equalTo($testValue),
                            $this->equalTo($testTimeout))
                    ->will($this->returnValue(true));

        $result = $this->client->set($testKey, $testValue, $testTimeout);

        $this->assertTrue($result);
    }

    public function testSetWrongTimeoutFloat()
    {
        $testKey = 'myTestKey';
        $testValue = 'myTestValue';
        $testTimeout = 0.0;

        $this->redis->expects($this->once())
            ->method('set')
            ->with( $this->equalTo($testKey),
                    $this->equalTo($testValue),
                    $this->equalTo(0))
            ->will($this->returnValue(true));

        $result = $this->client->set($testKey, $testValue, $testTimeout);

        $this->assertTrue($result);
    }

    public function testSetWrongTimeoutString()
    {
        $testKey = 'myTestKey';
        $testValue = 'myTestValue';
        $testTimeout = 'asdklaöldk';

        $this->redis->expects($this->once())
            ->method('set')
            ->with( $this->equalTo($testKey),
                    $this->equalTo($testValue),
                    $this->equalTo(0))
            ->will($this->returnValue(true));

        $result = $this->client->set($testKey, $testValue, $testTimeout);

        $this->assertTrue($result);
    }

    public function testSetEx()
    {
        $testKey = 'myTestKey';
        $testValue = 'myTestValue';
        $testTimeout = 22;

        $this->redis->expects($this->once())
            ->method('setex')
            ->with( $this->equalTo($testKey),
                $this->equalTo($testTimeout),
                $this->equalTo($testValue))
            ->will($this->returnValue(true));

        $result = $this->client->setex($testKey, $testTimeout, $testValue);

        $this->assertTrue($result);
    }

    public function testSetNx()
    {
        $testKey = 'myTestKey';
        $testValue = 'myTestValue';

        $this->redis->expects($this->once())
            ->method('setnx')
            ->with( $this->equalTo($testKey),
                $this->equalTo($testValue))
            ->will($this->returnValue(true));

        $result = $this->client->setnx($testKey, $testValue);

        $this->assertTrue($result);
    }

    public function testGet()
    {
        $testKey = 'myTestKey';

        $this->redis->expects($this->once())
            ->method('get')
            ->with($this->equalTo($testKey))
            ->will($this->returnValue(true));

        $result = $this->client->get($testKey);

        $this->assertTrue($result);
    }

    public function testAppend()
    {
        $testKey = 'myTestKey';
        $testValue1 = 'myTestValue1';
        $testValue2 = 'myTestValue2';

        $this->redis->expects($this->once())
            ->method('set')
            ->with( $this->equalTo($testKey),
                $this->equalTo($testValue1))
            ->will($this->returnValue(true));

        $this->redis->expects($this->once())
            ->method('append')
            ->with( $this->equalTo($testKey),
                $this->equalTo($testValue2))
            ->will($this->returnValue(12));

        $resultSet = $this->client->set($testKey, $testValue1);
        $this->assertTrue($resultSet);

        $resultAppend = $this->client->append($testKey, $testValue2);
        $this->assertEquals(12, $resultAppend);
    }

    public function testBitCount()
    {
        $testKey = 'myTestKey';

        $this->redis->expects($this->once())
            ->method('bitCount')
            ->with($this->equalTo($testKey))
            ->will($this->returnValue(12));

        $result = $this->client->bitCount($testKey);

        $this->assertEquals(12, $result);
    }

    public function testDecr()
    {
        $testKey = 'myTestKey';

        $this->redis->expects($this->once())
            ->method('decr')
            ->with($this->equalTo($testKey))
            ->will($this->returnValue(12));

        $result = $this->client->decr($testKey);

        $this->assertEquals(12, $result);
    }

    public function testGetBit()
    {
        $testKey = 'myTestKey';

        $this->redis->expects($this->once())
            ->method('getBit')
            ->with($this->equalTo($testKey)
                  , $this->equalTo(1))
            ->will($this->returnValue(1));

        $result = $this->client->getBit($testKey, 1);

        $this->assertEquals(1, $result);
    }

    public function testGetRange()
    {
        $testKey = 'myTestKey';

        $this->redis->expects($this->once())
            ->method('getRange')
            ->with($this->equalTo($testKey)
                   , $this->equalTo(1)
                   , $this->equalTo(11))
            ->will($this->returnValue('hello'));

        $result = $this->client->getRange($testKey, 1, 11);

        $this->assertEquals('hello', $result);
    }

    public function testGetSet()
    {
        $testKey = 'myTestKey';
        $value = 'my value';

        $this->redis->expects($this->once())
            ->method('getSet')
            ->with($this->equalTo($testKey)
                   , $this->equalTo($value))
            ->will($this->returnValue('hello'));

        $result = $this->client->getSet($testKey, $value);

        $this->assertEquals('hello', $result);
    }

    public function testIncr()
    {
        $testKey = 'myTestKey';

        $this->redis->expects($this->once())
            ->method('incr')
            ->with($this->equalTo($testKey))
            ->will($this->returnValue(3));

        $result = $this->client->incr($testKey);

        $this->assertEquals(3, $result);
    }

    public function testIncByFloat()
    {
        $testKey = 'myTestKey';
        $incr = 1.2;

        $this->redis->expects($this->once())
            ->method('incrByFloat')
            ->with($this->equalTo($testKey)
                   , $this->equalTo($incr))
            ->will($this->returnValue(3.2));

        $result = $this->client->incrByFloat($testKey, $incr);

        $this->assertEquals(3.2, $result);
    }

    public function testMget()
    {
        $keys = array('key1', 'key2', 'key3');
        $values = array('val1', 'val2', 'val3');

        $this->redis->expects($this->once())
            ->method('mget')
            ->with($this->equalTo($keys))
            ->will($this->returnValue($values));

        $result = $this->client->mget($keys);

        $this->assertEquals($values, $result);
    }

    public function testMset()
    {
        $keysValues = array('key1' => 'val1'
                            , 'key2' => 'val2'
                            , 'key3' => 'val3');

        $this->redis->expects($this->once())
            ->method('mset')
            ->with($this->equalTo($keysValues))
            ->will($this->returnValue(true));

        $result = $this->client->mset($keysValues);

        $this->assertTrue($result);
    }



}