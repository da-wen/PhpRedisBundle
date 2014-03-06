<?php
/**
 * Created by PhpStorm.
 * User: dawen
 * Date: 03.12.13
 * Time: 08:30
 */

namespace Dawen\Bundle\PhpRedisBundle\Tests\Component;

use Dawen\Bundle\PhpRedisBundle\Component\RedisClient;
use Dawen\Bundle\PhpRedisBundle\Component\RedisClientInterface;

class RedisClientServerTest extends \PHPUnit_Framework_TestCase
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

    public function testFlushAll()
    {

        $this->redis->expects($this->once())
            ->method('flushAll')
            ->will($this->returnValue(true));

        $result = $this->client->flushAll();

        $this->assertTrue($result);
    }

    public function testFlushDB()
    {

        $this->redis->expects($this->once())
            ->method('flushDB')
            ->will($this->returnValue(true));

        $result = $this->client->flushDB();

        $this->assertTrue($result);
    }

    public function testConfigGet()
    {

        $operation = RedisClientInterface::CONFIG_OPERATION_GET;
        $key = 'testKey';
        $return = array('a', 'b');

        $this->redis->expects($this->once())
            ->method('config')
            ->with($this->equalTo($operation),
                   $this->equalTo($key))
            ->will($this->returnValue($return));

        $result = $this->client->config($operation, $key);

        $this->assertSame($return, $result);
    }

    public function testConfigSet()
    {

        $operation = RedisClientInterface::CONFIG_OPERATION_SET;
        $key = 'testKey';
        $value = 'my val';
        $return = true;

        $this->redis->expects($this->once())
            ->method('config')
            ->with($this->equalTo($operation),
                   $this->equalTo($key),
                   $this->equalTo($value))
            ->will($this->returnValue($return));

        $result = $this->client->config($operation, $key, $value);

        $this->assertSame($return, $result);
    }

    public function testConfigSetNullVal()
    {

        $operation = RedisClientInterface::CONFIG_OPERATION_SET;
        $key = 'testKey';
        $value = null;
        $return = true;

        $this->redis->expects($this->once())
            ->method('config')
            ->with($this->equalTo($operation),
                $this->equalTo($key))
            ->will($this->returnValue($return));

        $result = $this->client->config($operation, $key, $value);

        $this->assertSame($return, $result);
    }

    public function testbgrewriteaof()
    {
        $return = true;

        $this->redis->expects($this->once())
            ->method('bgrewriteaof')
            ->will($this->returnValue($return));

        $result = $this->client->bgrewriteaof();

        $this->assertSame($return, $result);
    }

    public function testbgsave()
    {
        $return = true;

        $this->redis->expects($this->once())
            ->method('bgsave')
            ->will($this->returnValue($return));

        $result = $this->client->bgsave();

        $this->assertSame($return, $result);
    }


}