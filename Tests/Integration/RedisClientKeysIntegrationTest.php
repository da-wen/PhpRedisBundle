<?php
/**
 * Created by PhpStorm.
 * User: dawen
 * Date: 03.12.13
 * Time: 08:09
 */

namespace Dawen\Bundle\PhpRedisBundle\Tests\Integration;

use Dawen\Bundle\PhpRedisBundle\Component\RedisClient;
use Dawen\Bundle\PhpRedisBundle\Tests\Fixtures\AbstractKernelAwareTest;

class RedisClientKeysIntegrationTest extends AbstractKernelAwareTest
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

    public function testKeysEmpty()
    {
        $keys = $this->client->keys('*');
        $this->assertEmpty($keys);
    }

    public function testDelOneValue()
    {
        $key1 = 'key1';
        $val = 'val';
        $set = $this->client->set($key1, $val);
        $this->assertTrue($set);
        $keys = $this->client->keys('key*');
        $this->assertCount(1, $keys);

        //delete test
        $deleted = $this->client->del($key1);
        $this->assertEquals(1, $deleted);
        $keys = $this->client->keys('key*');
        $this->assertCount(0, $keys);
    }

    public function testDelThreeValue()
    {
        $key1 = 'key1';
        $key2 = 'key2';
        $key3 = 'key3';
        $key4 = 'key4';
        $val = 'val';
        $set1 = $this->client->set($key1, $val);
        $set2 = $this->client->set($key2, $val);
        $set3 = $this->client->set($key3, $val);
        $set4 = $this->client->set($key4, $val);
        $this->assertTrue($set1);
        $this->assertTrue($set2);
        $this->assertTrue($set3);
        $this->assertTrue($set4);
        $keys = $this->client->keys('key*');
        $this->assertCount(4, $keys);

        //delete test
        $deleted = $this->client->del($key1, $key2, $key3);
        $this->assertEquals(3, $deleted);
        $keys = $this->client->keys('key*');
        $this->assertCount(1, $keys);
        $this->assertTrue(in_array($key4, $keys));
    }

    public function testDelArray()
    {
        $key1 = 'key1';
        $key2 = 'key2';
        $key3 = 'key3';
        $key4 = 'key4';
        $val = 'val';
        $set1 = $this->client->set($key1, $val);
        $set2 = $this->client->set($key2, $val);
        $set3 = $this->client->set($key3, $val);
        $set4 = $this->client->set($key4, $val);
        $this->assertTrue($set1);
        $this->assertTrue($set2);
        $this->assertTrue($set3);
        $this->assertTrue($set4);
        $keys = $this->client->keys('key*');
        $this->assertCount(4, $keys);

        //delete test
        $deleted = $this->client->del(array($key1, $key2, $key3, $key4));
        $this->assertEquals(4, $deleted);
        $keys = $this->client->keys('key*');
        $this->assertCount(0, $keys);
    }

    public function testExists()
    {

        $key = 'testKey';

        $extists = $this->client->exists($key);
        $this->assertFalse($extists);

        $this->client->set($key, array('test1', 'test2'));

        $extists = $this->client->exists($key);
        $this->assertTrue($extists);
    }

    public function testKeysAll()
    {
        $this->client->set('key1', array('test1', 'test2'));
        $this->client->set('key2', 'test');

        $keys = $this->client->keys('*');
        $this->assertCount(2, $keys);
        $this->assertTrue(in_array('key1', $keys));
        $this->assertTrue(in_array('key2', $keys));
    }

    public function testKeysSpecial()
    {
        $this->client->set('key1', array('test1', 'test2'));
        $this->client->set('key2', 'test');
        $this->client->set('key10', 'test');

        $keys = $this->client->keys('key*');
        $this->assertCount(3, $keys);
        $this->assertTrue(in_array('key10', $keys));
        $this->assertTrue(in_array('key2', $keys));
        $this->assertTrue(in_array('key1', $keys));

        $this->client->set('mytest.10.test', 'test');
        $keys = $this->client->keys('mytest.*.test');
        $this->assertCount(1, $keys);
        $this->assertTrue(in_array('mytest.10.test', $keys));
    }

    public function testDump()
    {
        $key = 'myTestKey';
        $value = 'a test value';

        $success = $this->client->set($key, $value);
        $this->assertTrue($success);

        $result = $this->client->dump($key);
        $this->assertContains($value, $result);
    }

    public function testExpire()
    {
        $key = 'myTestKey';
        $value = 'a test value';
        $ttl = 1;

        $success = $this->client->set($key, $value);
        $this->assertTrue($success);

        $successExpire = $this->client->expire($key, $ttl);
        $this->assertTrue($successExpire);

        $result = $this->client->get($key);
        $this->assertEquals($value, $result);

        sleep(2);
        $result = $this->client->get($key);
        $this->assertFalse($result);
    }

    public function testExpireAt()
    {
        $key = 'myTestKey';
        $value = 'a test value';
        $ttl = 1;

        $success = $this->client->set($key, $value);
        $this->assertTrue($success);

        $successExpire = $this->client->expireAt($key, time() + $ttl);
        $this->assertTrue($successExpire);

        $result = $this->client->get($key);
        $this->assertEquals($value, $result);

        sleep(2);
        $result = $this->client->get($key);
        $this->assertFalse($result);
    }

//    public function testMigrate()
//    {
//        $key = 'myTestKey';
//        $value = 'a test value';
//        $ttl = 3600;
//
//        $success = $this->client->set($key, $value);
//        $this->assertTrue($success);
//
//        $successMigrate = $this->client->migrate($this->params['host']
//                                                 , $this->params['port']
//                                                 , $key
//                                                 , $this->params['db2']
//                                                 , $ttl);
//
//        //$this->assertTrue($successMigrate);
//
//        $successDb = $this->client->select($this->params['db2']);
//        $this->assertTrue($successDb);
//
//        $result = $this->client->get($key);
//        $this->assertEquals($value, $result);
//
//        $successFlushDb = $this->client->flushDB();
//        $this->assertTrue($successFlushDb);
//
//        $successDb = $this->client->select($this->params['db']);
//        $this->assertTrue($successDb);
//    }

    public function testMove()
    {
        $key = 'myTestKey';
        $value = 'a test value';

        $success = $this->client->set($key, $value);
        $this->assertTrue($success);

        $successMove = $this->client->move($key, $this->params['db2']);
        $this->assertTrue($successMove);

        $oldHasKey = $this->client->exists($key);
        $this->assertFalse($oldHasKey);

        $successDb = $this->client->select($this->params['db2']);
        $this->assertTrue($successDb);

        $result = $this->client->get($key);
        $this->assertEquals($value, $result);

        $successFlushDb = $this->client->flushDB();
        $this->assertTrue($successFlushDb);

        $successDb = $this->client->select($this->params['db']);
        $this->assertTrue($successDb);
    }

    public function testObject()
    {
        $key = 'myTestKey';
        $value = 'a test value';

        $success = $this->client->set($key, $value);
        $this->assertTrue($success);

        $resultEncoding = $this->client->object('encoding', $key);
        $this->assertContains($resultEncoding, array('raw', 'embstr'));

        $resultRefcount = $this->client->object('refcount', $key);
        $this->assertEquals(1, $resultRefcount);

        $resultIdletime = $this->client->object('idletime', $key);
        $this->assertTrue($resultIdletime <= 2);
    }

    public function testObjectException()
    {
        try
        {
            $resultEncoding = $this->client->object('blah', 'test');
        }
        catch(\InvalidArgumentException $exception)
        {
             $this->assertContains('string is not valid', $exception->getMessage());
            return true;
        }

        $this->assertTrue(false);
    }

    public function testPersist()
    {
        $key = 'myTestKey';
        $value = 'a test value';

        $success = $this->client->set($key, $value);
        $this->assertTrue($success);

        $notPersist = $this->client->persist($key);
        $this->assertFalse($notPersist);

        $successExpire = $this->client->expire($key, 10);
        $this->assertTrue($successExpire);

        $persist = $this->client->persist($key);
        $this->assertTrue($persist);
    }

    public function testRandomKeyNoKey()
    {
        $result = $this->client->randomKey();
        $this->assertFalse($result);
    }

    public function testRandomKey()
    {
        $key1 = 'myTestKey1';
        $value1 = 'a test value 1';
        $key2 = 'myTestKey2';
        $value2 = 'a test value 2';

        $data = array($key1 => $value1, $key2 => $value2);

        $successSet = $this->client->mset($data);
        $this->assertTrue($successSet);

        $randomKey = $this->client->randomKey();
        $this->assertTrue(in_array($randomKey, array_keys($data)));

        $randomVal = $this->client->get($randomKey);
        $this->assertTrue(in_array($randomVal, $data));
    }

    public function testRename()
    {
        $key = 'myTestKey';
        $dstKey = 'myTestRename';
        $value = 'a test value';

        $successSet = $this->client->set($key, $value);
        $this->assertTrue($successSet);

        $successRename = $this->client->rename($key, $dstKey);
        $this->assertTrue($successRename);

        $exists = $this->client->exists($dstKey);
        $this->assertTrue($exists);
    }

    public function testRenameNxSuccess()
    {
        $key = 'myTestKey';
        $dstKey = 'myTestRename';
        $value = 'a test value';

        $successSet = $this->client->set($key, $value);
        $this->assertTrue($successSet);

        $successRename = $this->client->renameNx($key, $dstKey);
        $this->assertTrue($successRename);

        $exists = $this->client->exists($dstKey);
        $this->assertTrue($exists);

        $exists = $this->client->exists($key);
        $this->assertFalse($exists);
    }

    public function testRenameNxError()
    {
        $key = 'myTestKey';
        $dstKey = 'myTestRename';
        $value = 'a test value';
        $value2 = 'a test value 2';

        $successSet1 = $this->client->set($key, $value);
        $this->assertTrue($successSet1);

        $successSet2 = $this->client->set($dstKey, $value2);
        $this->assertTrue($successSet2);

        $successRename = $this->client->renameNx($key, $dstKey);
        $this->assertFalse($successRename);

        $exists = $this->client->exists($key);
        $this->assertTrue($exists);

        $exists = $this->client->exists($dstKey);
        $this->assertTrue($exists);

        $res2 = $this->client->get($dstKey);
        $this->assertEquals($value2, $res2);
    }

    public function testType()
    {
        $key = 'myTestKey';
        $value = 'a test value';

        $successSet = $this->client->set($key, $value);
        $this->assertTrue($successSet);

        $type = $this->client->type($key);
        $this->assertEquals(\Redis::REDIS_STRING, $type);
    }

    public function testTypeNotFound()
    {
        $key = 'myTestKey';

        $type = $this->client->type($key);
        $this->assertEquals(\Redis::REDIS_NOT_FOUND, $type);
    }

    public function testTtl()
    {
        $key = 'myTestKey';
        $value = 'a test value';

        $successSet = $this->client->set($key, $value, 10);
        $this->assertTrue($successSet);

        $ttl = $this->client->ttl($key);
        $this->assertEquals(10, $ttl);
    }

    public function testTtlUnlimited()
    {
        $key = 'myTestKey';
        $value = 'a test value';

        $successSet = $this->client->set($key, $value);
        $this->assertTrue($successSet);

        $ttl = $this->client->ttl($key);
        $this->assertEquals(-1, $ttl);
    }

    public function testRestore()
    {
        $key = 'myTestKey';
        $value = 'a test value';
        $newKey = 'newKey';

        $successSet = $this->client->set($key, $value);
        $this->assertTrue($successSet);

        $resultValue = $this->client->dump($key);
        $result = $this->client->restore($newKey, 0, $resultValue);
        $this->assertTrue($result);

        $valueNewKey = $this->client->get($newKey);
        $this->assertEquals($value, $valueNewKey);
    }

    public function testScan()
    {
        $values = array('value1', 'value2', 'value3');
        $nonValues = array('nonValue');

        $this->client->flushDB();

        foreach (array_merge($values, $nonValues) as $key) {
            $this->assertTrue($this->client->set($key, 'true'));
        }

        while ($keys = $this->client->scan($iterator, 'value*')) {
            foreach ($keys as $key) {
                $this->assertContains($key, $values);
            }
        };
    }

    public function testPfAdd()
    {
        $key = 'pfkey';
        $valuesOne = array('one');
        $valuesTwo = array('one', 'two', 'three');

        $this->client->del($key);

        $this->assertEquals(false, $this->client->pfAdd($key, array()));
        $this->assertEquals(1, $this->client->pfAdd($key, $valuesOne));
        $this->assertEquals(0, $this->client->pfAdd($key, $valuesOne));
        $this->assertEquals(1, $this->client->pfAdd($key, $valuesTwo));
    }

    public function testPfCount()
    {
        $keyOne = 'pfkey1';
        $keyTwo = 'pfkey2';
        $valuesOne = array('one', 'two');
        $valuesTwo = array('two', 'three');

        $this->client->del($keyOne);
        $this->client->del($keyTwo);

        $this->assertEquals(1, $this->client->pfAdd($keyOne, $valuesOne));
        $this->assertEquals(1, $this->client->pfAdd($keyTwo, $valuesTwo));
        $this->assertEquals(2, $this->client->pfCount($keyOne));
        $this->assertEquals(2, $this->client->pfCount($keyTwo));
        $this->assertEquals(3, $this->client->pfCount(array($keyOne, $keyTwo)));
    }
}
