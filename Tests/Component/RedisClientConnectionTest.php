<?php
/**
 * Created by PhpStorm.
 * User: dawen
 * Date: 03.12.13
 * Time: 08:30
 */

namespace Dawen\Bundle\PhpRedisBundle\Tests\Component;

use Dawen\Bundle\PhpRedisBundle\Component\RedisClient;

class RedisClientConnectionTest extends \PHPUnit_Framework_TestCase
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

    public function testClose()
    {
        $this->redis->expects($this->once())
            ->method('close')
            ->will($this->returnValue(true));

        $this->client->close();
    }
}