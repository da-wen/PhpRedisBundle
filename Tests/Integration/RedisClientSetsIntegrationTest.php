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

class RedisClientSetsIntegrationTest extends AbstractKernelAwareTest
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

    public function testSAdd()
    {
        $key = 'testKey';
        $value1 = 'value1';

        $result = $this->client->sAdd($key, $value1);
        $this->assertEquals(1, $result);
    }

    public function testSAddParam2()
    {
        $key = 'testKey';
        $value1 = 'value1';
        $value2 = 'value2';

        $result = $this->client->sAdd($key, $value1, $value2);
        $this->assertEquals(2, $result);
    }

    public function testSAddParam3()
    {
        $key = 'testKey';
        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';

        $result = $this->client->sAdd($key, $value1, $value2, $value3);
        $this->assertEquals(3, $result);
    }

    public function testSCard()
    {
        $key = 'testKey';
        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';

        $result = $this->client->sAdd($key, $value1, $value2, $value3);
        $this->assertEquals(3, $result);

        $resultSCard = $this->client->sCard($key);
        $this->assertSame(3, $resultSCard);
    }

    public function testSSize()
    {
        $key = 'testKey';
        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';

        $result = $this->client->sAdd($key, $value1, $value2, $value3);
        $this->assertEquals(3, $result);

        $resultSSize = $this->client->sSize($key);
        $this->assertSame(3, $resultSSize);
    }

    public function testSDiff()
    {
        $key1 = 'testKey1';
        $key2 = 'testKey2';
        $key3 = 'testKey3';
        $key4 = 'testKey4';

        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';
        $value4 = 'value4';
        $value5 = 'value5';
        $value6 = 'value6';

        $result1 = $this->client->sAdd($key1, $value1, $value2, $value3);
        $this->assertEquals(3, $result1);

        $result2 = $this->client->sAdd($key2, $value4, $value5, $value6);
        $this->assertEquals(3, $result2);

        $result3 = $this->client->sAdd($key3, $value1, $value2, $value6);
        $this->assertEquals(3, $result3);

        $result4 = $this->client->sAdd($key4, 'hello');
        $this->assertEquals(1, $result4);

        $resultSDiff1 = $this->client->sDiff($key1, $key2);
        $this->assertContains($value1, $resultSDiff1);
        $this->assertContains($value2, $resultSDiff1);
        $this->assertContains($value3, $resultSDiff1);

        $resultSDiff2 = $this->client->sDiff($key2,$key1);
        $this->assertContains($value4, $resultSDiff2);
        $this->assertContains($value5, $resultSDiff2);
        $this->assertContains($value6, $resultSDiff2);

        $resultSDiff3 = $this->client->sDiff($key2, $key1);
        $this->assertContains($value6, $resultSDiff3);

        //problems with 3 keys. Tests for 3 is missing
    }

    public function testSDiffStore()
    {
        $dstKey = 'dstKey';
        $key1 = 'testKey1';
        $key2 = 'testKey2';

        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';
        $value4 = 'value4';
        $value5 = 'value5';
        $value6 = 'value6';

        $result1 = $this->client->sAdd($key1, $value1, $value2, $value3);
        $this->assertEquals(3, $result1);

        $result2 = $this->client->sAdd($key2, $value4, $value5, $value6);
        $this->assertEquals(3, $result2);

        $resultSDiffStore = $this->client->sDiffStore($dstKey, $key1, $key2);
        $this->assertEquals(3, $resultSDiffStore);

        $resultSSize = $this->client->sSize($dstKey);
        $this->assertEquals(3, $resultSSize);

        //problems with 3 keys. Tests for 3 is missing
    }

    public function testSInter()
    {
        $key1 = 'testKey1';
        $key2 = 'testKey2';
        $key3 = 'testKey3';

        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';
        $value4 = 'value4';
        $value5 = 'value5';
        $value6 = 'value6';

        $result1 = $this->client->sAdd($key1, $value1, $value2, $value3);
        $this->assertEquals(3, $result1);

        $result2 = $this->client->sAdd($key2, $value3, $value5, $value6);
        $this->assertEquals(3, $result2);

        $resultSInter = $this->client->sInter($key1, $key2);
        $this->assertCount(1, $resultSInter);
        $this->assertContains($value3, $resultSInter);

        //problems with 3 keys. Tests for 3 is missing
    }

    public function testSInterStore()
    {
        $key1 = 'testKey1';
        $key2 = 'testKey2';
        $dstKey = 'destKey';

        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';
        $value4 = 'value4';
        $value5 = 'value5';
        $value6 = 'value6';

        $result1 = $this->client->sAdd($key1, $value1, $value2, $value3);
        $this->assertEquals(3, $result1);

        $result2 = $this->client->sAdd($key2, $value3, $value5, $value6);
        $this->assertEquals(3, $result2);

        $resultSInterStore = $this->client->sInterStore($dstKey, $key1, $key2);
        $this->assertEquals(1, $resultSInterStore);

        //problems with 3 keys. Tests for 3 is missing
    }

    public function testSIsMember()
    {
        $key = 'testKey';

        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';

        $result = $this->client->sAdd($key, $value1, $value2, $value3);
        $this->assertEquals(3, $result);


        $resultSIsMember = $this->client->sIsMember($key, $value2);
        $this->assertTrue($resultSIsMember);
    }

    public function testSContains()
    {
        $key = 'testKey';

        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';

        $result = $this->client->sAdd($key, $value1, $value2, $value3);
        $this->assertEquals(3, $result);


        $resultSIsMember = $this->client->sContains($key, $value3);
        $this->assertTrue($resultSIsMember);
    }

    public function testSMembers()
    {
        $key = 'testKey';

        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';

        $result = $this->client->sAdd($key, $value1, $value2, $value3);
        $this->assertEquals(3, $result);

        $resultSMembers = $this->client->sMembers($key);
        $this->assertContains($value1, $resultSMembers);
        $this->assertContains($value2, $resultSMembers);
        $this->assertContains($value3, $resultSMembers);
    }

    public function testSGetMembers()
    {
        $key = 'testKey';

        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';

        $result = $this->client->sAdd($key, $value1, $value2, $value3);
        $this->assertEquals(3, $result);

        $resultSMembers = $this->client->sGetMembers($key);
        $this->assertContains($value1, $resultSMembers);
        $this->assertContains($value2, $resultSMembers);
        $this->assertContains($value3, $resultSMembers);
    }

    public function testSMove()
    {
        $key1 = 'testKey1';
        $key2 = 'testKey2';

        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';

        $result1 = $this->client->sAdd($key1, $value1, $value2, $value3);
        $this->assertEquals(3, $result1);

        $result2 = $this->client->sAdd($key2, $value3);
        $this->assertEquals(1, $result2);

        $resultSMembers = $this->client->sGetMembers($key1);
        $this->assertContains($value1, $resultSMembers);
        $this->assertContains($value2, $resultSMembers);
        $this->assertContains($value3, $resultSMembers);

        $resultSMembers = $this->client->sGetMembers($key2);
        $this->assertContains($value3, $resultSMembers);

        $resultMove = $this->client->sMove($key1, $key2, $value1);
        $this->assertTrue($resultMove);

        $resultSMembers3 = $this->client->sGetMembers($key1);
        $this->assertNotContains($value1, $resultSMembers3);
        $this->assertContains($value2, $resultSMembers3);
        $this->assertContains($value3, $resultSMembers3);

        $resultSMembers4 = $this->client->sGetMembers($key2);
        $this->assertContains($value3, $resultSMembers4);
        $this->assertContains($value1, $resultSMembers4);
    }

    public function testSPop()
    {
        $key = 'testKey';

        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';

        $result = $this->client->sAdd($key, $value1, $value2, $value3);
        $this->assertEquals(3, $result);

        $resultSPop = $this->client->sPop($key);
        $this->assertEquals(2, $this->client->sSize($key));
        $resultSMembers = $this->client->sMembers($key);
        $this->assertNotContains($resultSPop, $resultSMembers);
    }

    public function testSRandMember()
    {
        $key = 'testKey';

        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';

        $possibleValues = array($value1, $value2, $value3);

        $result = $this->client->sAdd($key, $value1, $value2, $value3);
        $this->assertEquals(3, $result);

        $resultSPop = $this->client->sRandMember($key);
        $this->assertContains($resultSPop, $possibleValues);
    }

    public function testSRemOne()
    {
        $key = 'testKey';

        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';
        $value4 = 'value4';
        $value5 = 'value5';

        $possibleValues = array($value1, $value2, $value3);

        $result = $this->client->sAdd($key, $value1, $value2, $value3);
        $this->assertEquals(3, $result);

        $result = $this->client->sAdd($key, $value4, $value5);
        $this->assertEquals(2, $result);

        $this->assertEquals(5, $this->client->sSize($key));

        $resultSRem = $this->client->sRem($key, $value1);
        $this->assertEquals(1, $resultSRem);

        $resultMembers = $this->client->sMembers($key);
        $this->assertNotContains($value1, $resultMembers);
    }

    public function testSRemTwo()
    {
        $key = 'testKey';

        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';
        $value4 = 'value4';
        $value5 = 'value5';

        $possibleValues = array($value1, $value2, $value3);

        $result = $this->client->sAdd($key, $value1, $value2, $value3);
        $this->assertEquals(3, $result);

        $result = $this->client->sAdd($key, $value4, $value5);
        $this->assertEquals(2, $result);

        $this->assertEquals(5, $this->client->sSize($key));

        $resultSRem = $this->client->sRem($key, $value1, $value2);
        $this->assertEquals(2, $resultSRem);

        $resultMembers = $this->client->sMembers($key);
        $this->assertNotContains($value1, $resultMembers);
        $this->assertNotContains($value2, $resultMembers);
    }

    public function testSRemThree()
    {
        $key = 'testKey';

        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';
        $value4 = 'value4';
        $value5 = 'value5';

        $possibleValues = array($value1, $value2, $value3);

        $result = $this->client->sAdd($key, $value1, $value2, $value3);
        $this->assertEquals(3, $result);

        $result = $this->client->sAdd($key, $value4, $value5);
        $this->assertEquals(2, $result);

        $this->assertEquals(5, $this->client->sSize($key));

        $resultSRem = $this->client->sRem($key, $value1, $value2, $value3);
        $this->assertEquals(3, $resultSRem);

        $resultMembers = $this->client->sMembers($key);
        $this->assertNotContains($value1, $resultMembers);
        $this->assertNotContains($value2, $resultMembers);
        $this->assertNotContains($value3, $resultMembers);
    }

    public function testSRemoveOne()
    {
        $key = 'testKey';

        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';
        $value4 = 'value4';
        $value5 = 'value5';

        $possibleValues = array($value1, $value2, $value3);

        $result = $this->client->sAdd($key, $value1, $value2, $value3);
        $this->assertEquals(3, $result);

        $result = $this->client->sAdd($key, $value4, $value5);
        $this->assertEquals(2, $result);

        $this->assertEquals(5, $this->client->sSize($key));

        $resultSRem = $this->client->sRemove($key, $value1);
        $this->assertEquals(1, $resultSRem);

        $resultMembers = $this->client->sMembers($key);
        $this->assertNotContains($value1, $resultMembers);
    }

    public function testSRemoveTwo()
    {
        $key = 'testKey';

        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';
        $value4 = 'value4';
        $value5 = 'value5';

        $possibleValues = array($value1, $value2, $value3);

        $result = $this->client->sAdd($key, $value1, $value2, $value3);
        $this->assertEquals(3, $result);

        $result = $this->client->sAdd($key, $value4, $value5);
        $this->assertEquals(2, $result);

        $this->assertEquals(5, $this->client->sSize($key));

        $resultSRem = $this->client->sRemove($key, $value1, $value2);
        $this->assertEquals(2, $resultSRem);

        $resultMembers = $this->client->sMembers($key);
        $this->assertNotContains($value1, $resultMembers);
        $this->assertNotContains($value2, $resultMembers);
    }

    public function testSRemoveThree()
    {
        $key = 'testKey';

        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';
        $value4 = 'value4';
        $value5 = 'value5';

        $possibleValues = array($value1, $value2, $value3);

        $result = $this->client->sAdd($key, $value1, $value2, $value3);
        $this->assertEquals(3, $result);

        $result = $this->client->sAdd($key, $value4, $value5);
        $this->assertEquals(2, $result);

        $this->assertEquals(5, $this->client->sSize($key));

        $resultSRem = $this->client->sRemove($key, $value1, $value2, $value3);
        $this->assertEquals(3, $resultSRem);

        $resultMembers = $this->client->sMembers($key);
        $this->assertNotContains($value1, $resultMembers);
        $this->assertNotContains($value2, $resultMembers);
        $this->assertNotContains($value3, $resultMembers);
    }

    public function testSUnion()
    {
        $key1 = 'testKey1';
        $key2 = 'testKey2';

        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';
        $value4 = 'value4';

        $result1 = $this->client->sAdd($key1, $value1, $value2);
        $this->assertEquals(2, $result1);

        $result2 = $this->client->sAdd($key2, $value3, $value4);
        $this->assertEquals(2, $result2);


        $resultSUnion = $this->client->sUnion($key1, $key2);
        $this->assertCount(4, $resultSUnion);

        $this->assertContains($value1, $resultSUnion);
        $this->assertContains($value2, $resultSUnion);
        $this->assertContains($value3, $resultSUnion);
        $this->assertContains($value4, $resultSUnion);
    }

    public function testSUnion3Params()
    {
        $key1 = 'testKey1';
        $key2 = 'testKey2';
        $key3 = 'testKey3';

        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value3';
        $value4 = 'value4';
        $value5 = 'value5';
        $value6 = 'value6';

        $result1 = $this->client->sAdd($key1, $value1, $value2);
        $this->assertEquals(2, $result1);

        $result2 = $this->client->sAdd($key2, $value3, $value4);
        $this->assertEquals(2, $result2);

        $result3 = $this->client->sAdd($key3, $value5, $value6);
        $this->assertEquals(2, $result3);


        $resultSUnion = $this->client->sUnion($key1, $key2, $key3);
        $this->assertCount(6, $resultSUnion);

        $this->assertContains($value1, $resultSUnion);
        $this->assertContains($value2, $resultSUnion);
        $this->assertContains($value3, $resultSUnion);
        $this->assertContains($value4, $resultSUnion);
        $this->assertContains($value5, $resultSUnion);
        $this->assertContains($value6, $resultSUnion);
    }

}