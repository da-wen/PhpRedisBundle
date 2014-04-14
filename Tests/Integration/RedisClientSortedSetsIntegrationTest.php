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




}