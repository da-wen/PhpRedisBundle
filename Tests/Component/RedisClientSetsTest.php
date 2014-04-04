<?php
/**
 * Created by PhpStorm.
 * User: dwendlandt
 * Date: 06/03/14
 * Time: 08:16
 */

namespace Dawen\Bundle\PhpRedisBundle\Tests\Component;

use Dawen\Bundle\PhpRedisBundle\Component\RedisClient;
use Dawen\Bundle\PhpRedisBundle\Component\RedisClientInterface;

class RedisClientSetsTest extends \PHPUnit_Framework_TestCase
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

    public function testSAdd()
    {

        $key = 'testKey';
        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';
        $return = 4;

        $this->redis->expects($this->once())
            ->method('sAdd')
            ->with($this->equalTo($key),
                   $this->equalTo($value1),
                   $this->equalTo($value2),
                   $this->equalTo($value3))
            ->will($this->returnValue($return));

        $result = $this->client->sAdd($key, $value1, $value2, $value3);

        $this->assertSame($return, $result);
    }

    public function testSCard()
    {
        $key = 'testKey';
        $return = 5;

        $this->redis->expects($this->once())
            ->method('sCard')
            ->with($this->equalTo($key))
            ->will($this->returnValue($return));

        $result = $this->client->sCard($key);

        $this->assertSame($return, $result);
    }

    public function testSSize()
    {
        $key = 'testKey';
        $return = 5;

        $this->redis->expects($this->once())
            ->method('sSize')
            ->with($this->equalTo($key))
            ->will($this->returnValue($return));

        $result = $this->client->sSize($key);

        $this->assertSame($return, $result);
    }

    public function testSDiff()
    {
        $key1 = 'testKey1';
        $key2 = 'testKey2';
        $return = array('foo', 'bar');

        $this->redis->expects($this->once())
            ->method('sDiff')
            ->with($this->equalTo($key1),
                   $this->equalTo($key2))
            ->will($this->returnValue($return));

        $result = $this->client->sDiff($key1, $key2);

        $this->assertSame($return, $result);
    }

    public function testSDiffStore()
    {
        $dstKey = 'dstKey';
        $key1 = 'testKey1';
        $key2 = 'testKey2';
        $return = array('foo', 'bar');

        $this->redis->expects($this->once())
            ->method('sDiffStore')
            ->with($this->equalTo($dstKey),
                   $this->equalTo($key1),
                   $this->equalTo($key2))
            ->will($this->returnValue($return));

        $result = $this->client->sDiffStore($dstKey, $key1, $key2);

        $this->assertSame($return, $result);
    }

    public function testSInter()
    {
        $key1 = 'testKey1';
        $key2 = 'testKey2';
        $return = array('foo', 'bar');

        $this->redis->expects($this->once())
            ->method('sInter')
            ->with($this->equalTo($key1),
                   $this->equalTo($key2))
            ->will($this->returnValue($return));

        $result = $this->client->sInter($key1, $key2);

        $this->assertSame($return, $result);
    }

    public function testSInterStore()
    {
        $dstKey = 'dstKey';
        $key1 = 'testKey1';
        $key2 = 'testKey2';
        $return = 1;

        $this->redis->expects($this->once())
            ->method('sInterStore')
            ->with($this->equalTo($dstKey),
                   $this->equalTo($key1),
                   $this->equalTo($key2))
            ->will($this->returnValue($return));

        $result = $this->client->sInterStore($dstKey, $key1, $key2);

        $this->assertSame($return, $result);
    }

    public function testSIsMember()
    {
        $key = 'testKey';
        $value = 'testValue';
        $return = true;

        $this->redis->expects($this->once())
            ->method('sIsMember')
            ->with($this->equalTo($key),
                   $this->equalTo($value))
            ->will($this->returnValue($return));

        $result = $this->client->sIsMember($key, $value);

        $this->assertSame($return, $result);
    }

    public function testSContains()
    {
        $key = 'testKey';
        $value = 'testValue';
        $return = true;

        $this->redis->expects($this->once())
            ->method('sContains')
            ->with($this->equalTo($key),
                $this->equalTo($value))
            ->will($this->returnValue($return));

        $result = $this->client->sContains($key, $value);

        $this->assertSame($return, $result);
    }

    public function testSMembers()
    {
        $key = 'testKey';
        $return = array('foo', 'bar');

        $this->redis->expects($this->once())
            ->method('sMembers')
            ->with($this->equalTo($key))
            ->will($this->returnValue($return));

        $result = $this->client->sMembers($key);

        $this->assertSame($return, $result);
    }

    public function testSGetMembers()
    {
        $key = 'testKey';
        $return = array('foo', 'bar');

        $this->redis->expects($this->once())
            ->method('sGetMembers')
            ->with($this->equalTo($key))
            ->will($this->returnValue($return));

        $result = $this->client->sGetMembers($key);

        $this->assertSame($return, $result);
    }

    public function testSMove()
    {
        $srcKey = 'srcKey';
        $dstKey = 'dstKey';
        $value = 'value';
        $return = true;

        $this->redis->expects($this->once())
            ->method('sMove')
            ->with($this->equalTo($srcKey),
                   $this->equalTo($dstKey),
                   $this->equalTo($value))
            ->will($this->returnValue($return));

        $result = $this->client->sMove($srcKey, $dstKey, $value);

        $this->assertSame($return, $result);
    }

    public function testSPop()
    {
        $key = 'testKey';
        $return = 'resultvalue';

        $this->redis->expects($this->once())
            ->method('sPop')
            ->with($this->equalTo($key))
            ->will($this->returnValue($return));

        $result = $this->client->sPop($key);

        $this->assertSame($return, $result);
    }


}