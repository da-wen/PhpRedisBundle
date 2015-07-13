<?php
/**
 * Created by PhpStorm.
 * User: dwendlandt
 * Date: 06/03/14
 * Time: 08:01
 */

namespace Dawen\Bundle\PhpRedisBundle\Tests\Integration;

use Dawen\Bundle\PhpRedisBundle\Component\RedisClient;
use Dawen\Bundle\PhpRedisBundle\Component\RedisClientInterface;
use Dawen\Bundle\PhpRedisBundle\Tests\Fixtures\AbstractKernelAwareTest;

class RedisClientSortedSetsIntegrationTest extends AbstractKernelAwareTest
{

    /** @var RedisClientInterface */
    private $client;

    private $skipped = false;

    public function setUp()
    {
        parent::setUp();

        if($this->container->hasParameter('redis'))
        {
            $redisParams = $this->container->getParameter('redis');
            if(!empty($redisParams['host']) && !empty($redisParams['port']))
            {
                $redis = new \Redis();
                $connected = $redis->pconnect($redisParams['host'], $redisParams['port']);
                if(!$connected) {
                    $this->skipped = true;
                    $this->markTestSkipped('could not connect to server');
                }
                $redis->select($redisParams['db']);

                $this->client = new RedisClient($redis);
            }
            else
            {
                $this->skipped = true;
                $this->markTestSkipped('parameter port and host must be set and filled');
            }
        }
        else
        {
            $this->skipped = true;
            $this->markTestSkipped('no parameters in config_test set');
        }
    }

    public function tearDown()
    {
        parent::tearDown();

        if(!$this->skipped)
        {
            $this->client->flushDB();
            $this->client->close();
        }

        $this->client = null;
        $this->skipped = false;
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Dawen\Bundle\PhpRedisBundle\Component\RedisClientInterface', $this->client);
    }

    public function testZAdd()
    {
        $key = 'testKey';
        $score1 = 1;
        $value1 = 'value1';

        $result = $this->client->zAdd($key, $score1, $value1);
        $this->assertEquals(1, $result);
    }

    public function testzAddParam2()
    {
        $key = 'testKey';
        $score1 = 1;
        $score2 = 3;
        $value1 = 'value1';
        $value2 = 'value2';

        $result = $this->client->zAdd($key, $score1, $value1, $score2, $value2);
        $this->assertEquals(2, $result);
    }

    public function testZAddParam3()
    {
        $key = 'testKey';
        $score1 = 1;
        $score2 = 2;
        $score3 = 3;
        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';

        $result = $this->client->zAdd($key, $score1, $value1, $score2, $value2, $score3, $value3);
        $this->assertEquals(3, $result);
    }

    public function testZCard()
    {
        $key = 'testKey';
        $score1 = 1;
        $score2 = 2;
        $score3 = 3;
        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';

        $result = $this->client->zAdd($key, $score1, $value1, $score2, $value2, $score3, $value3);
        $this->assertEquals(3, $result);

        $resultSCard = $this->client->zCard($key);
        $this->assertSame(3, $resultSCard);
    }

    public function testZSize()
    {
        $key = 'testKey';
        $score1 = 1;
        $score2 = 2;
        $score3 = 3;
        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';

        $result = $this->client->zAdd($key, $score1, $value1, $score2, $value2, $score3, $value3);
        $this->assertEquals(3, $result);

        $resultSSize = $this->client->zSize($key);
        $this->assertSame(3, $resultSSize);
    }

    public function testZCount()
    {
        $key = 'testKey';
        $score1 = 1;
        $score2 = 2;
        $score3 = 3;
        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';

        $result = $this->client->zAdd($key, $score1, $value1, $score2, $value2, $score3, $value3);
        $this->assertEquals(3, $result);

        $resultSSize = $this->client->zSize($key);
        $this->assertSame(3, $resultSSize);

        $this->assertEquals(2, $this->client->zCount($key, 0, 2));
    }

    public function testZIncrBy()
    {
        $key = 'testKey';
        $score1 = 1;
        $score2 = 2;
        $score3 = 3;
        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';

        $result = $this->client->zAdd($key, $score1, $value1, $score2, $value2, $score3, $value3);
        $this->assertEquals(3, $result);

        $this->assertEquals(4, $this->client->zIncrBy($key, 2, $value2));
        $this->assertEquals(1, $this->client->zCount($key, 4, 4));
    }

    public function testZInter()
    {
        $outKey = 'outKey';
        $key1 = 'testKey';
        $key2 = 'testKey';
        $score1 = 1;
        $score2 = 2;
        $score3 = 3;
        $score4 = 4;
        $score5 = 5;
        $score6 = 6;
        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';
        $value4 = 'value4';
        $value5 = 'value5';
        $value6 = 'value6';

        $result1 = $this->client->zAdd($key1, $score1, $value1, $score2, $value2, $score3, $value3);
        $this->assertEquals(3, $result1);

        $result2 = $this->client->zAdd($key2, $score4, $value4, $score5, $value5, $score6, $value6);
        $this->assertEquals(3, $result2);

        $resultZInter = $this->client->zInter($outKey, array($key1, $key2));
        $this->assertEquals(6, $resultZInter);

//        $this->assertEquals(6, $this->client->sSize($outKey));
    }

//    public function testZInterWeights()
//    {
//        $outKey = 'outKey';
//        $key1 = 'testKey';
//        $key2 = 'testKey';
//        $score1 = 1;
//        $score2 = 2;
//        $score3 = 3;
//        $score4 = 4;
//        $score5 = 5;
//        $score6 = 6;
//        $value1 = 'value1';
//        $value2 = 'value2';
//        $value3 = 'value3';
//        $value4 = 'value4';
//        $value5 = 'value5';
//        $value6 = 'value6';
//
//        $result1 = $this->client->zAdd($key1, $score1, $value1, $score2, $value2, $score3, $value3);
//        $this->assertEquals(3, $result1);
//
//        $result2 = $this->client->zAdd($key2, $score4, $value4, $score5, $value5, $score6, $value6);
//        $this->assertEquals(3, $result2);
//
//        $resultZInter = $this->client->zInter($outKey, array($key1, $key2), array(6,1));
//        $this->assertEquals(2, $resultZInter);
//
////        $this->assertEquals(6, $this->client->sSize($outKey));
//    }

    public function testZRange()
    {
        $key = 'testKey';
        $score1 = 1;
        $score2 = 2;
        $score3 = 3;
        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';

        $result = $this->client->zAdd($key, $score1, $value1, $score2, $value2, $score3, $value3);
        $this->assertEquals(3, $result);

        $resultRangeFull = $this->client->zRange($key, 0, -1);
        $this->assertCount(3, $resultRangeFull);
        $this->assertContains($value1, $resultRangeFull);
        $this->assertContains($value2, $resultRangeFull);
        $this->assertContains($value3, $resultRangeFull);

        $resultRangePart = $this->client->zRange($key, 0, 1);
        $this->assertCount(2, $resultRangePart);
        $this->assertContains($value1, $resultRangePart);
        $this->assertContains($value2, $resultRangePart);

    }

    public function testZRangeWithScores()
    {
        $key = 'testKey';
        $score1 = 1;
        $score2 = 2;
        $score3 = 3;
        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';

        $result = $this->client->zAdd($key, $score1, $value1, $score2, $value2, $score3, $value3);
        $this->assertEquals(3, $result);

        $resultRangeFull = $this->client->zRange($key, 0, -1, true);
        $this->assertCount(3, $resultRangeFull);
        $this->assertArrayHasKey($value1, $resultRangeFull);
        $this->assertArrayHasKey($value2, $resultRangeFull);
        $this->assertArrayHasKey($value3, $resultRangeFull);
        $this->assertEquals($score1, $resultRangeFull[$value1]);
        $this->assertEquals($score2, $resultRangeFull[$value2]);
        $this->assertEquals($score3, $resultRangeFull[$value3]);

        $resultRangePart = $this->client->zRange($key, 0, 1, true);
        $this->assertCount(2, $resultRangePart);
        $this->assertArrayHasKey($value1, $resultRangePart);
        $this->assertArrayHasKey($value2, $resultRangePart);
        $this->assertEquals($score1, $resultRangePart[$value1]);
        $this->assertEquals($score2, $resultRangePart[$value2]);

    }

    public function testZRangeByScore()
    {
        $key = 'testKey';
        $score1 = 1;
        $score2 = 2;
        $score3 = 3;
        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';

        $result = $this->client->zAdd($key, $score1, $value1, $score2, $value2, $score3, $value3);
        $this->assertEquals(3, $result);

        $resultRangeFull = $this->client->zRangeByScore($key, 0, 3);
        $this->assertCount(3, $resultRangeFull);
        $this->assertContains($value1, $resultRangeFull);
        $this->assertContains($value2, $resultRangeFull);
        $this->assertContains($value3, $resultRangeFull);
        $this->assertEquals($value1, $resultRangeFull[0]);

        $resultRangePart = $this->client->zRangeByScore($key, 0, 2);
        $this->assertCount(2, $resultRangePart);
        $this->assertContains($value1, $resultRangePart);
        $this->assertContains($value2, $resultRangePart);


    }

    public function testZRangeByScoreWithScores()
    {
        $key = 'testKey';
        $score1 = 1;
        $score2 = 2;
        $score3 = 3;
        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';

        $result = $this->client->zAdd($key, $score1, $value1, $score2, $value2, $score3, $value3);
        $this->assertEquals(3, $result);

        $resultRangeFull = $this->client->zRangeByScore($key, 0, 3, array('withscores' => true));
        $this->assertCount(3, $resultRangeFull);
        $this->assertArrayHasKey($value1, $resultRangeFull);
        $this->assertArrayHasKey($value2, $resultRangeFull);
        $this->assertArrayHasKey($value3, $resultRangeFull);
        $this->assertEquals($score1, $resultRangeFull[$value1]);
        $this->assertEquals($score2, $resultRangeFull[$value2]);
        $this->assertEquals($score3, $resultRangeFull[$value3]);

        $resultRangePart = $this->client->zRangeByScore($key, 0, 2, array('withscores' => true));
        $this->assertCount(2, $resultRangePart);
        $this->assertArrayHasKey($value1, $resultRangePart);
        $this->assertArrayHasKey($value2, $resultRangePart);
        $this->assertEquals($score1, $resultRangePart[$value1]);
        $this->assertEquals($score2, $resultRangePart[$value2]);

    }

    public function testZRevRangeByScore()
    {
        $key = 'testKey';
        $score1 = 1;
        $score2 = 2;
        $score3 = 3;
        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';

        $result = $this->client->zAdd($key, $score1, $value1, $score2, $value2, $score3, $value3);
        $this->assertEquals(3, $result);

        $resultRangeFull = $this->client->zRevRangeByScore($key, 3, 0);
        $this->assertCount(3, $resultRangeFull);
        $this->assertContains($value1, $resultRangeFull);
        $this->assertContains($value2, $resultRangeFull);
        $this->assertContains($value3, $resultRangeFull);
        $this->assertEquals($value3, $resultRangeFull[0]);

        $resultRangePart = $this->client->zRevRangeByScore($key, 2, 0);
        $this->assertCount(2, $resultRangePart);
        $this->assertContains($value1, $resultRangePart);
        $this->assertContains($value2, $resultRangePart);


    }

    public function testZRevRangeByScoreWithScores()
    {
        $key = 'testKey';
        $score1 = 1;
        $score2 = 2;
        $score3 = 3;
        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';

        $result = $this->client->zAdd($key, $score1, $value1, $score2, $value2, $score3, $value3);
        $this->assertEquals(3, $result);

        $resultRangeFull = $this->client->zRevRangeByScore($key, 3, 0, array('withscores' => true));
        $this->assertCount(3, $resultRangeFull);
        $this->assertArrayHasKey($value1, $resultRangeFull);
        $this->assertArrayHasKey($value2, $resultRangeFull);
        $this->assertArrayHasKey($value3, $resultRangeFull);
        $this->assertEquals($score1, $resultRangeFull[$value1]);
        $this->assertEquals($score2, $resultRangeFull[$value2]);
        $this->assertEquals($score3, $resultRangeFull[$value3]);

        $resultRangePart = $this->client->zRevRangeByScore($key, 2, 0, array('withscores' => true));
        $this->assertCount(2, $resultRangePart);
        $this->assertArrayHasKey($value1, $resultRangePart);
        $this->assertArrayHasKey($value2, $resultRangePart);
        $this->assertEquals($score1, $resultRangePart[$value1]);
        $this->assertEquals($score2, $resultRangePart[$value2]);

    }

    public function testZRank()
    {
        $key = 'testKey';
        $score1 = 1;
        $score2 = 2;
        $score3 = 3;
        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';

        $result = $this->client->zAdd($key, $score1, $value1, $score2, $value2, $score3, $value3);
        $this->assertEquals(3, $result);

        $this->assertEquals(0, $this->client->zRank($key, $value1));
        $this->assertEquals(1, $this->client->zRank($key, $value2));
        $this->assertEquals(2, $this->client->zRank($key, $value3));
    }

    public function testZRevRank()
    {
        $key = 'testKey';
        $score1 = 1;
        $score2 = 2;
        $score3 = 3;
        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';

        $result = $this->client->zAdd($key, $score1, $value1, $score2, $value2, $score3, $value3);
        $this->assertEquals(3, $result);

        $this->assertEquals(2, $this->client->zRevRank($key, $value1));
        $this->assertEquals(1, $this->client->zRevRank($key, $value2));
        $this->assertEquals(0, $this->client->zRevRank($key, $value3));
    }

    public function testZRem1Param()
    {
        $key = 'testKey';
        $score1 = 1;
        $score2 = 2;
        $score3 = 3;
        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';

        $result = $this->client->zAdd($key, $score1, $value1, $score2, $value2, $score3, $value3);
        $this->assertEquals(3, $result);

        $this->assertEquals(1, $this->client->zRem($key, $value1));
        $this->assertEquals(2, $this->client->zSize($key));
    }

    public function testZRem2Params()
    {
        $key = 'testKey';
        $score1 = 1;
        $score2 = 2;
        $score3 = 3;
        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';

        $result = $this->client->zAdd($key, $score1, $value1, $score2, $value2, $score3, $value3);
        $this->assertEquals(3, $result);

        $this->assertEquals(2, $this->client->zRem($key, $value1, $value2));
        $this->assertEquals(1, $this->client->zSize($key));
    }

    public function testZRem3Params()
    {
        $key = 'testKey';
        $score1 = 1;
        $score2 = 2;
        $score3 = 3;
        $score4 = 4;
        $score5 = 5;
        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';
        $value4 = 'value4';
        $value5 = 'value5';

        $result = $this->client->zAdd($key, $score1, $value1, $score2, $value2, $score3, $value3);
        $this->assertEquals(3, $result);

        $result = $this->client->zAdd($key, $score4, $value4, $score5, $value5);
        $this->assertEquals(2, $result);

        $this->assertEquals(5, $this->client->zSize($key));

        $this->assertEquals(3, $this->client->zRem($key, $value1, $value2, $value3));
        $this->assertEquals(2, $this->client->zSize($key));
    }

    public function testZDelete1Param()
    {
        $key = 'testKey';
        $score1 = 1;
        $score2 = 2;
        $score3 = 3;
        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';

        $result = $this->client->zAdd($key, $score1, $value1, $score2, $value2, $score3, $value3);
        $this->assertEquals(3, $result);

        $this->assertEquals(1, $this->client->zDelete($key, $value1));
        $this->assertEquals(2, $this->client->zSize($key));
    }

    public function testZDelete2Params()
    {
        $key = 'testKey';
        $score1 = 1;
        $score2 = 2;
        $score3 = 3;
        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';

        $result = $this->client->zAdd($key, $score1, $value1, $score2, $value2, $score3, $value3);
        $this->assertEquals(3, $result);

        $this->assertEquals(2, $this->client->zDelete($key, $value1, $value2));
        $this->assertEquals(1, $this->client->zSize($key));
    }

    public function testZDelete3Params()
    {
        $key = 'testKey';
        $score1 = 1;
        $score2 = 2;
        $score3 = 3;
        $score4 = 4;
        $score5 = 5;
        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';
        $value4 = 'value4';
        $value5 = 'value5';

        $result = $this->client->zAdd($key, $score1, $value1, $score2, $value2, $score3, $value3);
        $this->assertEquals(3, $result);

        $result = $this->client->zAdd($key, $score4, $value4, $score5, $value5);
        $this->assertEquals(2, $result);

        $this->assertEquals(5, $this->client->zSize($key));

        $this->assertEquals(3, $this->client->zDelete($key, $value1, $value2, $value3));
        $this->assertEquals(2, $this->client->zSize($key));
    }

    public function testZDeleteRangeByRange()
    {
        $key = 'testKey';
        $score1 = 1;
        $score2 = 2;
        $score3 = 3;
        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';

        $result = $this->client->zAdd($key, $score1, $value1, $score2, $value2, $score3, $value3);
        $this->assertEquals(3, $result);
        $this->assertEquals(3, $this->client->zSize($key));

        $this->assertEquals(2, $this->client->zDeleteRangeByRank($key, 0, 1));
        $this->assertEquals(1, $this->client->zSize($key));
    }

    public function testZRemRangeByRank()
    {
        $key = 'testKey';
        $score1 = 1;
        $score2 = 2;
        $score3 = 3;
        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';

        $result = $this->client->zAdd($key, $score1, $value1, $score2, $value2, $score3, $value3);
        $this->assertEquals(3, $result);
        $this->assertEquals(3, $this->client->zSize($key));

        $this->assertEquals(2, $this->client->zRemRangeByRank($key, 0, 1));
        $this->assertEquals(1, $this->client->zSize($key));
    }

    public function testZDeleteRangeByScore()
    {
        $key = 'testKey';
        $score1 = 1;
        $score2 = 2;
        $score3 = 3;
        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';

        $result = $this->client->zAdd($key, $score1, $value1, $score2, $value2, $score3, $value3);
        $this->assertEquals(3, $result);
        $this->assertEquals(3, $this->client->zSize($key));

        $this->assertEquals(2, $this->client->zDeleteRangeByScore($key, 0, 2));
        $this->assertEquals(1, $this->client->zSize($key));
    }

    public function testZRemRangeByScore()
    {
        $key = 'testKey';
        $score1 = 1;
        $score2 = 2;
        $score3 = 3;
        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';

        $result = $this->client->zAdd($key, $score1, $value1, $score2, $value2, $score3, $value3);
        $this->assertEquals(3, $result);
        $this->assertEquals(3, $this->client->zSize($key));

        $this->assertEquals(2, $this->client->zRemRangeByScore($key, 0, 2));
        $this->assertEquals(1, $this->client->zSize($key));
    }

    public function testZScore()
    {
        $key = 'testKey';
        $score1 = 1;
        $score2 = 2;
        $score3 = 3;
        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';

        $result = $this->client->zAdd($key, $score1, $value1, $score2, $value2, $score3, $value3);
        $this->assertEquals(3, $result);

        $this->assertEquals(1, $this->client->zScore($key, $value1));
        $this->assertEquals(2, $this->client->zScore($key, $value2));
        $this->assertEquals(3, $this->client->zScore($key, $value3));
    }

    public function testZUnion()
    {
        $output = 'testOut';
        $key1 = 'key1';
        $key2 = 'key2';
        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';
        $value4 = 'value4';
        $score1 = 1;
        $score2 = 2;
        $score3 = 3;
        $score4 = 4;

        $result = $this->client->zAdd($key1, $score1, $value1, $score2, $value2);
        $this->assertEquals(2, $result);

        $result = $this->client->zAdd($key2, $score3, $value3, $score4, $value4);
        $this->assertEquals(2, $result);

        $result = $this->client->zUnion($output, array($key1, $key2));
        $this->assertEquals(4, $result);

        $values = $this->client->zRange($output, 0, -1);
        $this->assertContains($value1, $values);
        $this->assertContains($value2, $values);
        $this->assertContains($value3, $values);
        $this->assertContains($value4, $values);
    }

    public function testZScan()
    {
        $key = 'zscan';
        $values = array('value1' => 1, 'value2' => 3);
        $nonValue = 'nonValue';

        $this->client->del($key);

        $this->assertEquals(3, $this->client->zAdd($key, 1, 'value1', 3, 'value2', 2, $nonValue));

        while ($members = $this->client->zScan($key, $iterator, 'value*')) {
            foreach ($members as $member => $score) {
                $this->assertContains($member, array_keys($values));
                $this->assertContains($score, array_values($values));
            }
        };
    }

    public function testZRangeByLex()
    {
        if (!version_compare(phpversion('redis'), '2.2.7', '>')) {
            $this->markTestSkipped('bugged in phpredis <= 2.2.7');
        }

        $key = 'range';

        $this->client->del($key);

        $this->assertEquals(4, $this->client->zAdd($key, 1, 'a', 2, 'b', 3, 'c', 4, 'd'));

        $this->assertEquals(
            array('a', 'b', 'c', 'd'),
            $this->client->zRangeByLex($key,  '-',  '+')
        );
        $this->assertEquals(
            array(),
            $this->client->zRangeByLex($key,  '+',  '-')
        );
        $this->assertEquals(
            array(),
            $this->client->zRangeByLex($key,  '-',  '-')
        );
        $this->assertEquals(
            array(),
            $this->client->zRangeByLex($key,  '+',  '+')
        );
        $this->assertEquals(
            array('a', 'b', 'c'),
            $this->client->zRangeByLex($key,  '-', '(d')
        );
        $this->assertEquals(
            array('a', 'b', 'c', 'd'),
            $this->client->zRangeByLex($key,  '-', '[d')
        );
        $this->assertEquals(
            array('b', 'c', 'd'),
            $this->client->zRangeByLex($key, '(a',  '+')
        );
        $this->assertEquals(
            array('a', 'b', 'c', 'd'),
            $this->client->zRangeByLex($key, '[a',  '+')
        );
        $this->assertEquals(
            array('b', 'c'),
            $this->client->zRangeByLex($key,  '-',  '+',  1, 2)
        );
        $this->assertEquals(
            array('a', 'b', 'c', 'd'),
            $this->client->zRangeByLex($key,  '-',  '+',  0, 5)
        );
        $this->assertEquals(
            array(),
            $this->client->zRangeByLex($key,  '-',  '+',  4, 3)
        );
        $this->assertEquals(
            array(),
            $this->client->zRangeByLex($key,  '-',  '+', -2, 4)
        );

        $this->setExpectedException('InvalidArgumentException');

        $this->client->zRangeByLex($key, '-', '+', 2);
    }

    public function testZRevRangeByLex()
    {
        if (!method_exists($this->client, 'zRevRangeByLex')) {
            $this->markTestSkipped('method missing in phpredis 2.2.7');
        }

        if (!version_compare(phpversion('redis'), '2.2.7', '>')) {
            $this->markTestSkipped('bugged in phpredis <= 2.2.7');
        }

        $key = 'range';

        $this->client->del($key);

        $this->assertEquals(4, $this->client->zAdd($key, 1, 'a', 2, 'b', 3, 'c', 4, 'd'));

        $this->assertEquals(
            array('d', 'c', 'b', 'a'),
            $this->client->zRevRangeByLex($key,  '+',  '-')
        );
        $this->assertEquals(
            array(),
            $this->client->zRevRangeByLex($key,  '-',  '+')
        );
        $this->assertEquals(
            array(),
            $this->client->zRevRangeByLex($key,  '-',  '-')
        );
        $this->assertEquals(
            array(),
            $this->client->zRevRangeByLex($key,  '+',  '+')
        );
        $this->assertEquals(
            array('c', 'b', 'a'),
            $this->client->zRevRangeByLex($key, '(d',  '-')
        );
        $this->assertEquals(
            array('d', 'c', 'b', 'a'),
            $this->client->zRevRangeByLex($key, '[d',  '-')
        );
        $this->assertEquals(
            array('d', 'c', 'b'),
            $this->client->zRevRangeByLex($key,  '+', '(a')
        );
        $this->assertEquals(
            array('d', 'c', 'b', 'a'),
            $this->client->zRevRangeByLex($key,  '+', '[a')
        );
        $this->assertEquals(
            array('c', 'b'),
            $this->client->zRevRangeByLex($key,  '+',  '-',  1, 2)
        );
        $this->assertEquals(
            array('d', 'c', 'b', 'a'),
            $this->client->zRevRangeByLex($key,  '+',  '-',  0, 5)
        );
        $this->assertEquals(
            array(),
            $this->client->zRevRangeByLex($key,  '+',  '-',  4, 3)
        );
        $this->assertEquals(
            array(),
            $this->client->zRevRangeByLex($key,  '+',  '-', -2, 4)
        );

        $this->setExpectedException('InvalidArgumentException');

        $this->client->zRevRangeByLex($key, '-', '+', 2);
    }
}
