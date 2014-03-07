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

    public function testdbSize()
    {
        $return = 4;

        $this->redis->expects($this->once())
            ->method('dbSize')
            ->will($this->returnValue($return));

        $result = $this->client->dbSize();

        $this->assertSame($return, $result);
    }

    public function testInfo()
    {
        $option = null;
        $return = array('testinfo' => 'value');

        $this->redis->expects($this->once())
                    ->method('info')
                    ->will($this->returnValue($return));

        $result = $this->client->info($option);

        $this->assertSame($return, $result);
    }

    public function testInfoParam()
    {
        $option = 'CPU';
        $return = array('testinfo' => 'value');

        $this->redis->expects($this->once())
            ->method('info')
                    ->with($this->equalTo($option))
            ->will($this->returnValue($return));

        $result = $this->client->info($option);

        $this->assertSame($return, $result);
    }

    public function testLastSave()
    {
        $return = 1028374682;

        $this->redis->expects($this->once())
            ->method('lastSave')
            ->will($this->returnValue($return));

        $result = $this->client->lastSave();

        $this->assertSame($return, $result);
    }

    public function testResetStat()
    {
        $return = true;

        $this->redis->expects($this->once())
            ->method('resetStat')
            ->will($this->returnValue($return));

        $result = $this->client->resetStat();

        $this->assertSame($return, $result);
    }

    public function testSave()
    {
        $return = true;

        $this->redis->expects($this->once())
            ->method('save')
            ->will($this->returnValue($return));

        $result = $this->client->save();

        $this->assertSame($return, $result);
    }

    public function testSlaveof()
    {
        $host = '111.111.111.222';
        $port = 12345;
        $return = 1;

        $this->redis->expects($this->once())
                    ->method('slaveof')
                    ->with($this->equalTo($host),
                           $this->equalTo($port))
                    ->will($this->returnValue($return));

        $result = $this->client->slaveof($host, $port);

        $this->assertSame($return, $result);
    }

    public function testTime()
    {
        $return = array('12313', '12312313');

        $this->redis->expects($this->once())
            ->method('time')
            ->will($this->returnValue($return));

        $result = $this->client->time();

        $this->assertSame($return, $result);
    }

    public function testSlowlog()
    {
        $operation = 'get';
        $length = 10;
        $return = array('12313', '12312313');

        $this->redis->expects($this->once())
                    ->method('slowlog')
                    ->with($this->equalTo($operation),
                           $this->equalTo($length))
                    ->will($this->returnValue($return));

        $result = $this->client->slowlog($operation, $length);

        $this->assertSame($return, $result);
    }
}