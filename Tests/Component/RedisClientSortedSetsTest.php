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

    public function testZCount()
    {
        $key = 'testKey';
        $start = 1;
        $end = 5;
        $return = 5;

        $this->redis->expects($this->once())
            ->method('zCount')
            ->with($this->equalTo($key),
                   $this->equalTo($start),
                   $this->equalTo($end))
            ->will($this->returnValue($return));

        $result = $this->client->zCount($key, $start, $end);

        $this->assertSame($return, $result);
    }

    public function testZIncrBy()
    {
        $key = 'testKey';
        $value = 3;
        $member = 'value2';
        $return = 5;

        $this->redis->expects($this->once())
            ->method('zIncrBy')
            ->with($this->equalTo($key),
                   $this->equalTo($value),
                   $this->equalTo($member))
            ->will($this->returnValue($return));

        $result = $this->client->zIncrBy($key, $value, $member);

        $this->assertSame($return, $result);
    }

    public function testZInter()
    {
        $out = 'testKey';
        $keys = array('key1', 'key2');
        $weights = array(1.4);
        $methods = 'SUM';
        $return = 5;

        $this->redis->expects($this->once())
            ->method('zInter')
            ->with($this->equalTo($out),
                   $this->equalTo($keys),
                   $this->equalTo($weights),
                   $this->equalTo($methods))
            ->will($this->returnValue($return));

        $result = $this->client->zInter($out, $keys, $weights, $methods);

        $this->assertSame($return, $result);
    }

    public function testZRange()
    {
        $key = 'testKey';
        $start = 0;
        $end = -1;
        $withScores = true;
        $return = array('value1' => 1, 'value2' => 2);

        $this->redis->expects($this->once())
            ->method('zRange')
            ->with($this->equalTo($key),
                   $this->equalTo($start),
                   $this->equalTo($end),
                   $this->equalTo($withScores))
            ->will($this->returnValue($return));

        $result = $this->client->zRange($key, $start, $end, $withScores);

        $this->assertSame($return, $result);
    }

    public function testZRangeByScore()
    {
        $key = 'testKey';
        $start = 0;
        $end = 3;
        $options = array();
        $return = array('value1' => 1, 'value2' => 2);

        $this->redis->expects($this->once())
            ->method('zRangeByScore')
            ->with($this->equalTo($key),
                $this->equalTo($start),
                $this->equalTo($end),
                $this->equalTo($options))
            ->will($this->returnValue($return));

        $result = $this->client->zRangeByScore($key, $start, $end, $options);

        $this->assertSame($return, $result);
    }

    public function testZRevRangeByScore()
    {
        $key = 'testKey';
        $start = 0;
        $end = 3;
        $options = array();
        $return = array('value1' => 1, 'value2' => 2);

        $this->redis->expects($this->once())
            ->method('zRevRangeByScore')
            ->with($this->equalTo($key),
                $this->equalTo($start),
                $this->equalTo($end),
                $this->equalTo($options))
            ->will($this->returnValue($return));

        $result = $this->client->zRevRangeByScore($key, $start, $end, $options);

        $this->assertSame($return, $result);
    }

    public function testZRank()
    {
        $key = 'testKey';
        $member = 'value2';
        $return = 2;

        $this->redis->expects($this->once())
            ->method('zRank')
            ->with($this->equalTo($key),
                $this->equalTo($member))
            ->will($this->returnValue($return));

        $result = $this->client->zRank($key, $member);

        $this->assertSame($return, $result);
    }

    public function testZRevRank()
    {
        $key = 'testKey';
        $member = 'value2';
        $return = 2;

        $this->redis->expects($this->once())
            ->method('zRevRank')
            ->with($this->equalTo($key),
                $this->equalTo($member))
            ->will($this->returnValue($return));

        $result = $this->client->zRevRank($key, $member);

        $this->assertSame($return, $result);
    }

    public function testZRem()
    {

        $key = 'testKey';
        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';
        $return = 4;

        $this->redis->expects($this->once())
            ->method('zRem')
            ->with($this->equalTo($key),
                $this->equalTo($value1),
                $this->equalTo($value2),
                $this->equalTo($value3))
            ->will($this->returnValue($return));

        $result = $this->client->zRem($key, $value1, $value2, $value3);

        $this->assertSame($return, $result);
    }

    public function testZDelete()
    {

        $key = 'testKey';
        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';
        $return = 4;

        $this->redis->expects($this->once())
            ->method('zDelete')
            ->with($this->equalTo($key),
                $this->equalTo($value1),
                $this->equalTo($value2),
                $this->equalTo($value3))
            ->will($this->returnValue($return));

        $result = $this->client->zDelete($key, $value1, $value2, $value3);

        $this->assertSame($return, $result);
    }

    public function testZDeleteRangeByRank()
    {

        $key = 'testKey';
        $start = 0;
        $end = 1;
        $return = 2;

        $this->redis->expects($this->once())
            ->method('zDeleteRangeByRank')
            ->with($this->equalTo($key),
                $this->equalTo($start),
                $this->equalTo($end))
            ->will($this->returnValue($return));

        $result = $this->client->zDeleteRangeByRank($key, $start, $end);

        $this->assertSame($return, $result);
    }

    public function testZRemRangeByRank()
    {

        $key = 'testKey';
        $start = 0;
        $end = 1;
        $return = 2;

        $this->redis->expects($this->once())
            ->method('zRemRangeByRank')
            ->with($this->equalTo($key),
                $this->equalTo($start),
                $this->equalTo($end))
            ->will($this->returnValue($return));

        $result = $this->client->zRemRangeByRank($key, $start, $end);

        $this->assertSame($return, $result);
    }

    public function testZDeleteRangeByScore()
    {
        $key = 'testKey';
        $start = 0;
        $end = 1;
        $return = 2;

        $this->redis->expects($this->once())
            ->method('zDeleteRangeByScore')
            ->with($this->equalTo($key),
                $this->equalTo($start),
                $this->equalTo($end))
            ->will($this->returnValue($return));

        $result = $this->client->zDeleteRangeByScore($key, $start, $end);

        $this->assertSame($return, $result);
    }

    public function testZRemRangeByScore()
    {
        $key = 'testKey';
        $start = 0;
        $end = 1;
        $return = 2;

        $this->redis->expects($this->once())
            ->method('zRemRangeByScore')
            ->with($this->equalTo($key),
                $this->equalTo($start),
                $this->equalTo($end))
            ->will($this->returnValue($return));

        $result = $this->client->zRemRangeByScore($key, $start, $end);

        $this->assertSame($return, $result);
    }

    public function testZScore()
    {
        $key = 'testKey';
        $member = 'member1';
        $return = 2.5;

        $this->redis->expects($this->once())
            ->method('zScore')
            ->with($this->equalTo($key),
                   $this->equalTo($member))
            ->will($this->returnValue($return));

        $result = $this->client->zScore($key, $member);

        $this->assertSame($return, $result);
    }

}