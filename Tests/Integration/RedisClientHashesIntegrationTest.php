<?php
/**
 * Created by PhpStorm.
 * User: dawen
 * Date: 17.01.14
 * Time: 08:10
 */

namespace Dawen\Bundle\PhpRedisBundle\Tests\Integration;

use Dawen\Bundle\PhpRedisBundle\Component\RedisClient;
use Dawen\Bundle\PhpRedisBundle\Tests\Fixtures\AbstractKernelAwareTest;

class RedisClientHashesIntegrationTest extends AbstractKernelAwareTest
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

    public function testHSetInsert()
    {
        $key = 'myTestKey';
        $hashKey = 'myHashKey';
        $value = 'test value';

        $resultSet = $this->client->hSet($key, $hashKey, $value);
        $this->assertEquals(1, $resultSet);

        $resultGet = $this->client->hGet($key, $hashKey);
        $this->assertEquals($value, $resultGet);
    }

    public function testHSetReplace()
    {
        $key = 'myTestKey';
        $hashKey = 'myHashKey';
        $value = 'test value';
        $valueReplace = 'new value';

        $resultSet = $this->client->hSet($key, $hashKey, $value);
        $this->assertEquals(1, $resultSet);

        $resultGet = $this->client->hGet($key, $hashKey);
        $this->assertEquals($value, $resultGet);

        $resultReplace = $this->client->hSet($key, $hashKey, $valueReplace);
        $this->assertEquals(0, $resultReplace);

        $resultReplace = $this->client->hGet($key, $hashKey);
        $this->assertEquals($valueReplace, $resultReplace);
    }

    public function testHGet()
    {
        $key = 'myTestKey';
        $hashKey = 'myHashKey';
        $value = 'test value';

        $resultSet = $this->client->hSet($key, $hashKey, $value);
        $this->assertEquals(1, $resultSet);

        $resultGet = $this->client->hGet($key, $hashKey);
        $this->assertEquals($value, $resultGet);

        $resultNoHashKey = $this->client->hGet($key, 'hashKeyMissing');
        $this->assertFalse($resultNoHashKey);
    }

    public function testHDelOneVal()
    {
        $key = 'myTestKey';
        $hashKey = 'myHashKey';
        $value = 'test value';

        $resultSet = $this->client->hSet($key, $hashKey, $value);
        $this->assertEquals(1, $resultSet);

        $resultGet = $this->client->hGet($key, $hashKey);
        $this->assertEquals($value, $resultGet);

        $resultDel = $this->client->hDel($key, $hashKey);
        $this->assertEquals(1, $resultDel);

        $resultNoHashKey = $this->client->hGet($key, $hashKey);
        $this->assertFalse($resultNoHashKey);
    }

    public function testHDelTwoVal()
    {
        $key = 'myTestKey';
        $hashKey = 'myHashKey';
        $hashKey2 = 'myHashKey2';
        $value = 'test value';
        $value2 = 'test value2';

        $resultSet = $this->client->hSet($key, $hashKey, $value);
        $this->assertEquals(1, $resultSet);

        $resultSet2 = $this->client->hSet($key, $hashKey2, $value2);
        $this->assertEquals(1, $resultSet2);

        $resultGet = $this->client->hGet($key, $hashKey);
        $this->assertEquals($value, $resultGet);

        $resultGet = $this->client->hGet($key, $hashKey2);
        $this->assertEquals($value2, $resultGet);

        $resultDel = $this->client->hDel($key, $hashKey, $hashKey2);
        $this->assertEquals(2, $resultDel);

        $resultNoHashKey = $this->client->hGet($key, $hashKey);
        $this->assertFalse($resultNoHashKey);

        $resultNoHashKey = $this->client->hGet($key, $hashKey2);
        $this->assertFalse($resultNoHashKey);
    }

    public function testHDelThreeVal()
    {
        $key = 'myTestKey';
        $hashKey = 'myHashKey';
        $hashKey2 = 'myHashKey2';
        $hashKey3 = 'myHashKey3';
        $value = 'test value';
        $value2 = 'test value2';
        $value3 = 'test value3';

        $resultSet = $this->client->hSet($key, $hashKey, $value);
        $this->assertEquals(1, $resultSet);

        $resultSet2 = $this->client->hSet($key, $hashKey2, $value2);
        $this->assertEquals(1, $resultSet2);

        $resultSet3 = $this->client->hSet($key, $hashKey3, $value3);
        $this->assertEquals(1, $resultSet3);

        $resultGet = $this->client->hGet($key, $hashKey);
        $this->assertEquals($value, $resultGet);

        $resultGet = $this->client->hGet($key, $hashKey2);
        $this->assertEquals($value2, $resultGet);

        $resultGet = $this->client->hGet($key, $hashKey3);
        $this->assertEquals($value3, $resultGet);

        $resultDel = $this->client->hDel($key, $hashKey, $hashKey2, $hashKey3);
        $this->assertEquals(3, $resultDel);

        $resultNoHashKey = $this->client->hGet($key, $hashKey);
        $this->assertFalse($resultNoHashKey);

        $resultNoHashKey = $this->client->hGet($key, $hashKey2);
        $this->assertFalse($resultNoHashKey);

        $resultNoHashKey = $this->client->hGet($key, $hashKey3);
        $this->assertFalse($resultNoHashKey);
    }

    public function testHExists()
    {
        $key = 'myTestKey';
        $hashKey = 'myHashKey';
        $value = 'test value';

        $resultSet = $this->client->hSet($key, $hashKey, $value);
        $this->assertEquals(1, $resultSet);

        $resultExistsTrue = $this->client->hExists($key, $hashKey);
        $this->assertTrue($resultExistsTrue);

        $resultExistsFalse = $this->client->hExists($key, 'noHashKey');
        $this->assertFalse($resultExistsFalse);

        $resultExistsKeyFalse = $this->client->hExists('noKey', 'noHashKey');
        $this->assertFalse($resultExistsKeyFalse);
    }

    public function testHGetAll()
    {
        $key = 'myTestKey';
        $hashObj = array();

        for($i=1; $i<=5; $i++) {
            $hashKey = 'hashKey' . $i;
            $hashValue = 'hashValue' . $i;
            $hashObj[$hashKey] = $hashValue;

            $resultSet = $this->client->hSet($key, $hashKey, $hashValue);
            $this->assertEquals(1, $resultSet);
        }

        $resultHashObject = $this->client->hGetAll($key);
        $this->assertEquals($hashObj, $resultHashObject);
    }

    public function testHGetAllNoKey()
    {
        $resultHashObject = $this->client->hGetAll('noKey');
        $this->assertEmpty($resultHashObject);
    }

    public function testHIncrBy()
    {
        $key = 'myTestKey';
        $hashKey = 'hashKey';
        $value = 5;
        $incr = 7;

        $resultFirst = $this->client->hIncrBy($key, $hashKey, $value);
        $this->assertEquals($value, $resultFirst);

        $resultIncr = $this->client->hIncrBy($key, $hashKey, $incr);
        $this->assertEquals($value + $incr, $resultIncr);
    }

    public function testHIncrByString()
    {
        $key = 'myTestKey';
        $hashKey = 'hashKey';
        $value = 'string4';
        $incr = 7;

        $resultFirst = $this->client->hIncrBy($key, $hashKey, $value);
        $this->assertFalse($resultFirst);

        $resultSet = $this->client->hSet($key, $hashKey, $value);
        $this->assertEquals(1, $resultSet);

        $resultIncr = $this->client->hIncrBy($key, $hashKey, $incr);
        $this->assertFalse($resultIncr);
    }

    public function testHIncrByFloat()
    {
        $key = 'myTestKey';
        $hashKey = 'hashKey';
        $value = 5.2;
        $incr = 7.2;

        $resultFirst = $this->client->hSet($key, $hashKey, $value);
        $this->assertEquals(1, $resultFirst);

        $resultIncr = $this->client->hIncrByFloat($key, $hashKey, $incr);
        $this->assertEquals($value + $incr, $resultIncr);
    }

    public function testHIncrByFloatInt()
    {
        $key = 'myTestKey';
        $hashKey = 'hashKey';
        $value = 5;
        $incr = 7;

        $resultFirst = $this->client->hSet($key, $hashKey, $value);
        $this->assertEquals(1, $resultFirst);

        $resultIncr = $this->client->hIncrByFloat($key, $hashKey, $incr);
        $this->assertEquals($value + $incr, $resultIncr);
    }

    public function testHIncrByFloatIntFloat()
    {
        $key = 'myTestKey';
        $hashKey = 'hashKey';
        $value = 5;
        $incr = 7.4;

        $resultFirst = $this->client->hSet($key, $hashKey, $value);
        $this->assertEquals(1, $resultFirst);

        $resultIncr = $this->client->hIncrByFloat($key, $hashKey, $incr);
        $this->assertEquals($value + $incr, $resultIncr);
    }

    public function testHIncrByFloatNoHash()
    {
        $key = 'myTestKey';
        $hashKey = 'hashKey';
        $value = 5;

        $resultFirst = $this->client->hIncrByFloat($key, $hashKey, $value);
        $this->assertEquals(5.0, $resultFirst);
    }

    public function testHIncrByFloatString()
    {
        $key = 'myTestKey';
        $hashKey = 'hashKey';
        $value = 5;
        $incr = 'st7.4';

        $resultFirst = $this->client->hSet($key, $hashKey, $value);
        $this->assertEquals(1, $resultFirst);

        try {
            $resultIncr = $this->client->hIncrByFloat($key, $hashKey, $incr);
        } catch(\Exception $exxception) {
            $this->assertContains('to be double, string given',$exxception->getMessage());
            return true;
        }

        $this->fail();
    }

    public function testHKeys()
    {
        $key = 'myTestKey';
        $hashKeys = array('hashKey1', 'hashKey2', 'hashKey3');

        foreach($hashKeys as $hashKey)
        {
            $resultSet = $this->client->hSet($key, $hashKey, rand(1,20));
            $this->assertEquals(1, $resultSet);
        }

        $resultKeys = $this->client->hKeys($key);
        $this->assertEquals($hashKeys, $resultKeys);
    }

    public function testHKeysNoKey()
    {
        $resultKeys = $this->client->hKeys('noKeySet');
        $this->assertEmpty($resultKeys);
    }

    public function testHLen()
    {
        $key = 'myTestKey';
        $hashKeys = array('hashKey1', 'hashKey2', 'hashKey3');

        foreach($hashKeys as $hashKey)
        {
            $resultSet = $this->client->hSet($key, $hashKey, rand(1,20));
            $this->assertEquals(1, $resultSet);
        }

        $resultKeys = $this->client->hLen($key);
        $this->assertEquals(count($hashKeys), $resultKeys);
    }

    public function testHLensNoKey()
    {
        $resultKeys = $this->client->hKeys('noKeySet');
        $this->assertEmpty($resultKeys);
    }

    public function testHMGet()
    {
        $key = 'myTestKey';
        $hashKeys = array('hashKey1' => 300, 'hashKey2' => 'test2', 'hashKey3' => 23);

        foreach($hashKeys as $hashKey => $hashValue)
        {
            $resultSet = $this->client->hSet($key, $hashKey, $hashValue);
            $this->assertEquals(1, $resultSet);
        }

        $resultMGet = $this->client->hMGet($key, array_keys($hashKeys));
        $this->assertEquals($hashKeys, $resultMGet);
    }


}