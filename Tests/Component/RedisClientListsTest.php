<?php
/**
 * Created by PhpStorm.
 * User: dawen
 * Date: 06/02/14
 * Time: 08:19
 */

namespace Dawen\Bundle\PhpRedisBundle\Tests\Component;

use Dawen\Bundle\PhpRedisBundle\Component\RedisClient;

class RedisClientListsTest extends \PHPUnit_Framework_TestCase
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

    public function testLPush()
    {
        $key = 'testkey';
        $value = 'testValue';

        $this->redis->expects($this->once())
            ->method('lPush')
            ->with( $this->equalTo($key)
                , $this->equalTo($value))
            ->will($this->returnValue(true));

        $result = $this->client->lPush($key, $value);

        $this->assertTrue($result);
    }

    public function testLGet()
    {
        $key = 'testkey';
        $index = 2;
        $value = 'testValue';

        $this->redis->expects($this->once())
            ->method('lGet')
            ->with( $this->equalTo($key)
                , $this->equalTo($index))
            ->will($this->returnValue($value));

        $result = $this->client->lGet($key, $index);

        $this->assertEquals($value, $result);
    }

    public function testLIndex()
    {
        $key = 'testkey';
        $index = 2;
        $value = 'testValue';

        $this->redis->expects($this->once())
            ->method('lIndex')
            ->with( $this->equalTo($key)
                , $this->equalTo($index))
            ->will($this->returnValue($value));

        $result = $this->client->lIndex($key, $index);

        $this->assertEquals($value, $result);
    }

    public function testLSet()
    {
        $key = 'testkey';
        $index = 2;
        $value = 'testValue';

        $this->redis->expects($this->once())
            ->method('lSet')
            ->with( $this->equalTo($key)
                , $this->equalTo($index)
                , $this->equalTo($value))
            ->will($this->returnValue(true));

        $result = $this->client->lSet($key, $index, $value);

        $this->assertTrue($result);
    }

    public function testBlPop()
    {
        $keys = array('testkey', 'key2', 'key3');
        $return = array('testValue');

        $this->redis->expects($this->once())
            ->method('blPop')
            ->with($this->equalTo($keys))
            ->will($this->returnValue($return));

        $result = $this->client->blPop($keys);

        $this->assertEquals($return, $result);
    }

    public function testBrPop()
    {
        $keys = array('testkey', 'key2', 'key3');
        $return = array('testValue');

        $this->redis->expects($this->once())
            ->method('brPop')
            ->with($this->equalTo($keys))
            ->will($this->returnValue($return));

        $result = $this->client->brPop($keys);

        $this->assertEquals($return, $result);
    }

}