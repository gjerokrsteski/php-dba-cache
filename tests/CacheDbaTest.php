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
        $this->_cache = new CacheDba($path);
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
        $this->_cache = new CacheDba($path);
        unlink($path);
    }

    public function testCreateNewCacheObjectNotpersistentlyNoException()
    {
        $path = dirname(dirname(__FILE__)).'/tests/_drafts/test-cache.flat';
        $this->_cache = new CacheDba($path, 'c', 'flatfile', false);
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

    public function testCreateAnCdbHandler()
    {
        try
        {
            $path = dirname(dirname(__FILE__)).'/tests/_drafts/simple-xml-test-cache-on-cdb.db';
            $cache = new CacheDba($path, "n", "cdb", true);
        }
        catch (Exception $e)
        {
            $this->fail(
                $e->getMessage().$e->getTraceAsString()
            );
        }
    }
}