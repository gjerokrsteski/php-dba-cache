<?php
require_once dirname(dirname(__FILE__)).'/src/CacheDba.php';
require_once dirname(dirname(__FILE__)).'/src/CacheSerializer.php';

class CacheDbaHandlersTest
extends PHPUnit_Framework_TestCase
{
    protected $_object;

    protected $_identifier;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();

        $stdClass = new stdClass();
        $stdClass->title = 'Zweiundvierz';
        $stdClass->from = 'Joe';
        $stdClass->to = 'Jane';
        $stdClass->body = 'Ich kenne die Antwort!';

        $this->_object = $stdClass;
        $this->_identifier = md5('stdClass'.time());
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        unset($this->_object, $this->_identifier);
        parent::tearDown();
    }

    /**
     * Tests support for Oracle Berkeley DB 4.
     */
    public function testOracleBerkeleyDb4HandlerSupportWithoutPersistantConnection()
    {
        $path = dirname(dirname(__FILE__)).'/tests/_drafts/test-cache.db4';
        $cache = new CacheDba($path, new CacheSerializer(), 'c', 'db4', false);

        $this->assertInstanceOf('CacheDba', $cache);

        $cache->put($this->_identifier, $this->_object);

        $this->assertInstanceOf('stdClass', $cache->get($this->_identifier));
    }

    /**
     * @depends CacheDbaHandlersTest::testOracleBerkeleyDb4HandlerSupportWithoutPersistantConnection
     */
    public function testOracleBerkeleyDb4HandlerBeSupportedWithPersistantConnection()
    {
        $path = dirname(dirname(__FILE__)).'/tests/_drafts/test-cache.db4';
        $cache = new CacheDba($path, new CacheSerializer(), 'c', 'db4', true);

        $this->assertInstanceOf('CacheDba', $cache);

        $cache->put($this->_identifier, $this->_object);

        $this->assertInstanceOf('stdClass', $cache->get($this->_identifier));
    }

    /**
     * Tests support for CDB - Tiny Constant Database.
     */
    public function testCanCdbHandlerOnlyNewAndReadBeSupportedWithPersistantConnection()
    {
        $path = dirname(dirname(__FILE__)).'/tests/_drafts/test-cache-cdb2.cdb';

        // create handler to write.
        $cacheMake = new CacheDba($path, new CacheSerializer(), 'n', 'cdb_make', true);

        $this->assertInstanceOf('CacheDba', $cacheMake);

        $this->assertTrue($cacheMake->put(md5('test123'), $this->_object));

        // for read we close the handler.
        $cacheMake->closeDba();

        // create handler to read.
        $cacheRead = new CacheDba($path, new CacheSerializer(), 'r', 'cdb', true);

        $this->assertTrue($cacheRead->has(md5('test123')));

        $this->assertInstanceOf('stdClass', $cacheRead->get(md5('test123')));

        $cacheRead->closeDba();
    }
}