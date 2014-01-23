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
}