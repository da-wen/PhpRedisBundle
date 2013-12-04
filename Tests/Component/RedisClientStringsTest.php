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

}