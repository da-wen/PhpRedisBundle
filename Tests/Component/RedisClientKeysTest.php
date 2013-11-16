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