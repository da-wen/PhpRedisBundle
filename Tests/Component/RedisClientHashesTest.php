<?php
/**
 * Created by PhpStorm.
 * User: dawen
 * Date: 17.01.14
 * Time: 08:11
 */

namespace Dawen\Bundle\PhpRedisBundle\Tests\Component;

use Dawen\Bundle\PhpRedisBundle\Component\RedisClient;

class RedisClientHashesTest extends \PHPUnit_Framework_TestCase
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

    public function testHGet()
    {
        $key = 'testkey';
        $hashKey = 'testHashKey';

        $this->redis->expects($this->once())
            ->method('hGet')
            ->with( $this->equalTo($key)
                , $this->equalTo($hashKey))
            ->will($this->returnValue('test'));

        $result = $this->client->hGet($key, $hashKey);

        $this->assertEquals('test', $result);
    }

    public function testHSet()
    {
        $key = 'testkey';
        $hashKey = 'testHashKey';
        $value = "testValue";

        $this->redis->expects($this->once())
                    ->method('hSet')
                    ->with( $this->equalTo($key)
                            , $this->equalTo($hashKey)
                            , $this->equalTo($value))
                    ->will($this->returnValue(1));

        $result = $this->client->hSet($key, $hashKey, $value);

        $this->assertEquals(1, $result);
    }

    public function testHDelOneVal()
    {
        $key = 'testkey';
        $hashKey = 'testHashKey';

        $this->redis->expects($this->once())
            ->method('hDel')
            ->with( $this->equalTo($key)
                    , $this->equalTo($hashKey)
                    , $this->equalTo(null)
                    , $this->equalTo(null))
            ->will($this->returnValue(1));

        $result = $this->client->hDel($key, $hashKey);

        $this->assertEquals(1, $result);
    }

    public function testHDelThreeVal()
    {
        $key = 'testkey';
        $hashKey = 'testHashKey';
        $hashKey2 = 'testHashKey2';
        $hashKey3 = 'testHashKey3';

        $this->redis->expects($this->once())
            ->method('hDel')
            ->with( $this->equalTo($key)
                , $this->equalTo($hashKey)
                , $this->equalTo($hashKey2)
                , $this->equalTo($hashKey3))
            ->will($this->returnValue(1));

        $result = $this->client->hDel($key, $hashKey, $hashKey2, $hashKey3);

        $this->assertEquals(1, $result);
    }

    public function testHExists()
    {
        $key = 'testkey';
        $hashKey = 'testHashKey';

        $this->redis->expects($this->once())
            ->method('hExists')
            ->with( $this->equalTo($key)
                , $this->equalTo($hashKey))
            ->will($this->returnValue(true));

        $result = $this->client->hExists($key, $hashKey);

        $this->assertTrue($result);
    }

    public function testHGetAll()
    {
        $key = 'testkey';
        $return = array('one' => 1, 'two' => 'three');

        $this->redis->expects($this->once())
            ->method('hGetAll')
            ->with( $this->equalTo($key))
            ->will($this->returnValue($return));

        $result = $this->client->hGetAll($key);

        $this->assertEquals($return, $result);
    }

    public function testHIncrBy()
    {
        $key = 'testkey';
        $hashKey = 'testHashKey';
        $value = 12;
        $return = 24;

        $this->redis->expects($this->once())
            ->method('hIncrBy')
            ->with( $this->equalTo($key)
                , $this->equalTo($hashKey)
                , $this->equalTo($value))
            ->will($this->returnValue($return));

        $result = $this->client->hIncrBy($key, $hashKey, $value);

        $this->assertEquals($return, $result);
    }

    public function testHIncrByFloat()
    {
        $key = 'testkey';
        $hashKey = 'testHashKey';
        $value = 1.8;
        $return = 3.7;

        $this->redis->expects($this->once())
            ->method('hIncrByFloat')
            ->with( $this->equalTo($key)
                , $this->equalTo($hashKey)
                , $this->equalTo($value))
            ->will($this->returnValue($return));

        $result = $this->client->hIncrByFloat($key, $hashKey, $value);

        $this->assertEquals($return, $result);
    }

    public function testHKeys()
    {
        $key = 'testkey';
        $return = array('a', 'b');

        $this->redis->expects($this->once())
            ->method('hKeys')
            ->with( $this->equalTo($key))
            ->will($this->returnValue($return));

        $result = $this->client->hKeys($key);

        $this->assertEquals($return, $result);
    }

    public function testHLen()
    {
        $key = 'testkey';
        $return = 6;

        $this->redis->expects($this->once())
            ->method('hLen')
            ->with( $this->equalTo($key))
            ->will($this->returnValue($return));

        $result = $this->client->hLen($key);

        $this->assertEquals($return, $result);
    }

    public function testHMGet()
    {
        $key = 'testkey';
        $hashKeys = array('one', 'two');
        $return = array('one' => 1, 'two' => 2);

        $this->redis->expects($this->once())
            ->method('hMGet')
            ->with( $this->equalTo($key)
                    , $this->equalTo($hashKeys))
            ->will($this->returnValue($return));

        $result = $this->client->hMGet($key, $hashKeys);

        $this->assertEquals($return, $result);
    }

    public function testHMSet()
    {
        $key = 'testkey';
        $hashKeys = array('one1' => 111, 'two2' => 222);
        $return = array('one' => 1, 'two' => 2);

        $this->redis->expects($this->once())
            ->method('hMSet')
            ->with( $this->equalTo($key)
                    , $this->equalTo($hashKeys))
            ->will($this->returnValue($return));

        $result = $this->client->hMSet($key, $hashKeys);

        $this->assertEquals($return, $result);
    }

    public function testHSetNx()
    {
        $key = 'testkey';
        $hashKey = 'testHashKey';
        $value = "testValue";

        $this->redis->expects($this->once())
            ->method('hSetNx')
            ->with( $this->equalTo($key)
                , $this->equalTo($hashKey)
                , $this->equalTo($value))
            ->will($this->returnValue(true));

        $result = $this->client->hSetNx($key, $hashKey, $value);

        $this->assertTrue($result);
    }

    public function testHVals()
    {
        $key = 'testkey';
        $value = array(1, 'two2');

        $this->redis->expects($this->once())
            ->method('hVals')
            ->with( $this->equalTo($key))
            ->will($this->returnValue($value));

        $result = $this->client->hVals($key);

        $this->assertEquals($value, $result);
    }
}