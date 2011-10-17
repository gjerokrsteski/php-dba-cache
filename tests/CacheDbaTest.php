<?php
require_once dirname(dirname(__FILE__)).'/src/CacheDba.php';
require_once dirname(dirname(__FILE__)).'/src/CacheSerializer.php';

class CacheDbaTest
extends PHPUnit_Framework_TestCase
{
    private $_cache;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();

        $path = dirname(dirname(__FILE__)).'/tests/_drafts/cache.flat';
        $this->_cache = new CacheDba($path, new CacheSerializer());
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        unset($this->_cache);

        parent::tearDown();
    }

    public function testCreateNewCacheObjectNoException()
    {
        $path = dirname(dirname(__FILE__)).'/tests/_drafts/test-cache.flat';
        $this->_cache = new CacheDba($path, new CacheSerializer());
        unlink($path);
    }

    public function testCreateNewCacheObjectNotpersistentlyNoException()
    {
        $path = dirname(dirname(__FILE__)).'/tests/_drafts/test-cache.flat';
        $this->_cache = new CacheDba($path, new CacheSerializer(), 'c', 'flatfile', false);
        unlink($path);
    }

    public function objectsProvider()
    {
        $stdClass = new stdClass();
        $stdClass->title = 'Zweiundvierz';
        $stdClass->from = 'Joe';
        $stdClass->to = 'Jane';
        $stdClass->body = 'Ich kenne die Antwort -- aber was ist die Frage?';

        return array(
          array(md5('stdClass'), $stdClass),
          array(md5('ZipArchive'), new ZipArchive()),
          array(md5('XMLReader'), new XMLReader()),
        );
    }

    /**
     * @depends CacheDbaTest::testCreateNewCacheObjectNoException
     * @dataProvider objectsProvider
     */
    public function testPutSomeObjectsIntoTheCache($identifier, $object)
    {
        try
        {
            $this->_cache->put($identifier, $object);
        }
        catch (Exception $e)
        {
            $this->fail(
                $e->getMessage().$e->getTraceAsString()
            );
        }
    }

    /**
     * @depends CacheDbaTest::testPutSomeObjectsIntoTheCache
     * @dataProvider objectsProvider
     */
    public function testGetSomeObjectsFromTheCacheAndCompareEachother($identifier, $expectedObject)
    {
        try
        {
            $this->assertTrue($this->_cache->has($identifier));

            $getObject = $this->_cache->get($identifier);

            $this->assertEquals($expectedObject, $getObject);
        }
        catch (Exception $e)
        {
            $this->fail(
                $e->getMessage().$e->getTraceAsString()
            );
        }
    }

    public function testReadAllIdsInCache()
    {
        $path = dirname(dirname(__FILE__)).'/tests/_drafts/test-cache-get-all-ids.flat';
        $cache = new CacheDba($path, new CacheSerializer(), 'c', 'flatfile', false);

        $cache->put('array-1', array(1));
        $cache->put('string-2', 'some big string');
        $cache->put('float-3', 1234.87987698);

        $ids = $cache->getIds();

        $this->assertInternalType('array', $ids);
        $this->assertEquals($ids[0], 'array-1');
        $this->assertEquals($ids[1], 'string-2');
        $this->assertEquals($ids[2], 'float-3');

        unlink($path);
    }

    public function testCreateAnCdbHandler()
    {
        try
        {
            $path = dirname(dirname(__FILE__)).'/tests/_drafts/simple-xml-test-cache-on-cdb.db';
            $cache = new CacheDba($path, new CacheSerializer(), "n", "cdb", true);
            unlink($path);
        }
        catch (Exception $e)
        {
            $this->fail(
                $e->getMessage().$e->getTraceAsString()
            );
        }
    }

    public function testPutTheSameIdentifierTwiceToCdbHandler()
    {
        $path = dirname(dirname(__FILE__)).'/tests/_drafts/test-cache-cdb2.cdb';

        // CREATE HANDLER TO WRITE.
        $cacheMake = new CacheDba($path, new CacheSerializer(), 'n', 'cdb_make', true);

        // first insert.
        $this->assertTrue($cacheMake->put('key', 'data'));

        // replace instead of insert.
        $this->assertTrue($cacheMake->put('key', 'data-2'));

        // for read we close the handler.
        $cacheMake->closeDba();

        // CREATE HANDLER TO READ.
        $cacheRead = new CacheDba($path, new CacheSerializer(), 'r', 'cdb', true);

        //check if data replaced.
        $this->assertEquals('data', $cacheRead->get('key'));

        $cacheRead->closeDba();

        unlink($path);
    }

    public function testPutTheSameIdentifierTwiceToFlatfileHandler()
    {
        $path = dirname(dirname(__FILE__)).'/tests/_drafts/test-cache-insert.flat';
        $cache = new CacheDba($path, new CacheSerializer(), 'c', 'flatfile', false);

        // first insert.
        $this->assertTrue($cache->put('key', 'data'));

        // replace instead of insert.
        $this->assertTrue($cache->put('key', 'data-2'));

        //check if data replaced.
        $this->assertEquals('data-2', $cache->get('key'));
    }

    public function testPutTheSameIdentifierTwiceToDb4Handler()
    {
        $path = dirname(dirname(__FILE__)).'/tests/_drafts/test-cache.db4';
        $cache = new CacheDba($path, new CacheSerializer(), 'c', 'db4', true);

        // first insert.
        $this->assertTrue($cache->put('key', 'data'));

        // replace instead of insert.
        $this->assertTrue($cache->put('key', 'data-2'));

        //check if data replaced.
        $this->assertEquals('data-2', $cache->get('key'));
    }
}