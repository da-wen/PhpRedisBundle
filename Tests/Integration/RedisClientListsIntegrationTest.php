<?php
/**
 * Created by PhpStorm.
 * User: dawen
 * Date: 06/02/14
 * Time: 07:39
 */

namespace Dawen\Bundle\PhpRedisBundle\Tests\Integration;

use Dawen\Bundle\PhpRedisBundle\Component\RedisClient;
use Dawen\Bundle\PhpRedisBundle\Tests\Fixtures\AbstractKernelAwareTest;

class RedisClientListsIntegrationTest extends AbstractKernelAwareTest
{

    /** @var RedisClient */
    private $client;
    private $skipped = false;
    private $params;


    public function setUp()
    {
        parent::setUp();

        if($this->container->hasParameter('redis'))
        {
            $redisParams = $this->container->getParameter('redis');
            $this->params = $redisParams;
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
        $this->params = null;
        $this->skipped = false;
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Dawen\Bundle\PhpRedisBundle\Component\RedisClientInterface', $this->client);
    }

    public function testLPushAndLGet()
    {
        $key = 'myKey';
        $value = 'hello test';

        $resultPush = $this->client->lPush($key, $value);
        $this->assertEquals(1, $resultPush);

        $resultGet = $this->client->lGet($key, 0);
        $this->assertEquals($value, $resultGet);
    }

    public function testLPushTwoVals()
    {
        $key = 'myKey';
        $value1 = 'hello test';
        $value2 = 'val2';

        $resultPush = $this->client->lPush($key, $value1, $value2);
        $this->assertEquals(2, $resultPush);

        $resultGet1 = $this->client->lGet($key, 1);
        $this->assertEquals($value1, $resultGet1);

        $resultGet2 = $this->client->lGet($key, 0);
        $this->assertEquals($value2, $resultGet2);
    }

    public function testLPushThreeVals()
    {
        $key = 'myKey';
        $value1 = 'hello test';
        $value2 = 'val2';
        $value3 = 'val3';

        $resultPush = $this->client->lPush($key, $value1, $value2, $value3);
        $this->assertEquals(3, $resultPush);

        $resultGet1 = $this->client->lGet($key, 2);
        $this->assertEquals($value1, $resultGet1);

        $resultGet2 = $this->client->lGet($key, 1);
        $this->assertEquals($value2, $resultGet2);

        $resultGet3 = $this->client->lGet($key, 0);
        $this->assertEquals($value3, $resultGet3);
    }

    public function testLIndex()
    {
        $key = 'myKey';
        $value = 'hello test';

        $resultPush = $this->client->lPush($key, $value);
        $this->assertEquals(1, $resultPush);

        $resultIndex = $this->client->lIndex($key, 0);
        $this->assertEquals($value, $resultIndex);
    }

    public function testLSet()
    {
        $key = 'myKey';
        $value_orig = 'original value';
        $value_new = 'new value';

        $resultPush = $this->client->lPush($key, $value_orig);
        $this->assertEquals(1, $resultPush);

        $resultIndex = $this->client->lIndex($key, 0);
        $this->assertEquals($value_orig, $resultIndex);

        $resultSet = $this->client->lSet($key, 0, $value_new);
        $this->assertTrue($resultSet);

        $resultGet = $this->client->lGet($key, 0);
        $this->assertEquals($value_new, $resultGet);
    }

    public function testLSetNoList()
    {
        $key = 'myKey';
        $value_orig = 'original value';

        $resultSet = $this->client->lSet($key, 5, $value_orig);
        $this->assertFalse($resultSet);
    }

    public function testBrPoplPush()
    {
        $srcKey = 'srcKey';
        $dstKey = 'dstKey';
        $value = 'myValue';
        $value2 = 'my second value';

        $resultPush = $this->client->lPush($srcKey, $value, $value2);
        $this->assertEquals(2, $resultPush);

        $resultGet1 = $this->client->lGet($srcKey, 1);
        $this->assertEquals($value, $resultGet1);

        $resultGet0 = $this->client->lGet($srcKey, 0);
        $this->assertEquals($value2, $resultGet0);

        $resultBrPoplPush = $this->client->brPoplPush($srcKey, $dstKey, 0);
        $this->assertSame($value, $resultBrPoplPush);

        $resultSrcKeyExists = $this->client->exists($srcKey);
        $this->assertTrue($resultSrcKeyExists);

        $resultDstKeyExists = $this->client->exists($dstKey);
        $this->assertTrue($resultDstKeyExists);

        $resultSrc = $this->client->lGet($srcKey, 0);
        $this->assertEquals($value2, $resultSrc);

        $resultDst = $this->client->lGet($dstKey, 0);
        $this->assertEquals($value, $resultDst);

    }

}