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


}