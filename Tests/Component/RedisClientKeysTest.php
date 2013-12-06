<?php
/**
 * Created by PhpStorm.
 * User: dawen
 * Date: 16.11.13
 * Time: 21:07
 */

namespace Dawen\Bundle\PhpRedisBundle\Tests\Component;

use Dawen\Bundle\PhpRedisBundle\Component\RedisClient;

class RedisClientKeysTest extends \PHPUnit_Framework_TestCase
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

    public function testDelAllParams()
    {
        $key1 = 'testkey1';
        $key2 = 'testkey2';
        $key3 = 'testkey3';

        $this->redis->expects($this->once())
            ->method('del')
            ->with( $this->equalTo($key1)
                    , $this->equalTo($key2)
                    , $this->equalTo($key3))
            ->will($this->returnValue(3));

        $result = $this->client->del($key1, $key2, $key3);

        $this->assertEquals(3, $result);
    }

    public function testDelArrayParam()
    {
        $keys = array('key1', 'key2', 'key3', 'key4');

        $this->redis->expects($this->once())
            ->method('del')
            ->with( $this->equalTo($keys))
            ->will($this->returnValue(4));

        $result = $this->client->del($keys);

        $this->assertEquals(4, $result);
    }

    public function testExists()
    {
        $key = 'testkey';

        $this->redis->expects($this->once())
            ->method('exists')
            ->with( $this->equalTo($key))
            ->will($this->returnValue(true));

        $result = $this->client->exists($key);

        $this->assertTrue($result);
    }

    public function testKeys()
    {
        $pattern = 'users.admin.*';
        $testValue = array('users.admin.myadmin', 'users.admin.TesterAdmin');

        $this->redis->expects($this->once())
            ->method('keys')
            ->with( $this->equalTo($pattern))
            ->will($this->returnValue($testValue));

        $result = $this->client->keys($pattern);

        $this->assertSame($testValue, $result);
    }
}