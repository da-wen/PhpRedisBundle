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

class RedisClientSortedSetsTest extends \PHPUnit_Framework_TestCase
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

    public function testZAdd()
    {

        $key = 'testKey';
        $score1 = 1;
        $score2 = 2;
        $score3 = 3;
        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';
        $return = 4;

        $this->redis->expects($this->once())
            ->method('zAdd')
            ->with($this->equalTo($key),
                   $this->equalTo($score1),
                   $this->equalTo($value1),
                   $this->equalTo($score2),
                   $this->equalTo($value2),
                   $this->equalTo($score3),
                   $this->equalTo($value3))
            ->will($this->returnValue($return));

        $result = $this->client->zAdd($key, $score1, $value1, $score2, $value2, $score3, $value3);

        $this->assertSame($return, $result);
    }

    public function testZCard()
    {
        $key = 'testKey';
        $return = 5;

        $this->redis->expects($this->once())
            ->method('zCard')
            ->with($this->equalTo($key))
            ->will($this->returnValue($return));

        $result = $this->client->zCard($key);

        $this->assertSame($return, $result);
    }

    public function testZSize()
    {
        $key = 'testKey';
        $return = 5;

        $this->redis->expects($this->once())
            ->method('zSize')
            ->with($this->equalTo($key))
            ->will($this->returnValue($return));

        $result = $this->client->zSize($key);

        $this->assertSame($return, $result);
    }


}