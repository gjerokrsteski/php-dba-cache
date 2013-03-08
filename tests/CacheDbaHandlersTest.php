<?php
require_once dirname(__FILE__) .'/DummyFixtures.php';


class CacheHandlersTest extends PHPUnit_Framework_TestCase
{
  /**
   * @var stdClass
   */
  protected $_object;

  /**
   * @var string
   */
  protected $_identifier;

  /**
   * Prepares the environment before running a test.
   */
  protected function setUp()
  {
    parent::setUp();

    $stdClass        = new stdClass();
    $stdClass->title = 'Zweiundvierz';
    $stdClass->from  = 'Joe';
    $stdClass->to    = 'Jane';
    $stdClass->body  = new Dummy();

    $this->_object     = $stdClass;
    $this->_identifier = md5('stdClass' . time());
  }

  /**
   * Cleans up the environment after running a test.
   */
  protected function tearDown()
  {
    unset($this->_object, $this->_identifier);
    parent::tearDown();
  }

  /**
   * Tests support for Oracle Berkeley DB 4.
   */
  public function testOracleBerkeleyDb4HandlerSupportWithoutPersistantConnection()
  {
    try {
      $cache = new Cache(
        dirname(dirname(__FILE__)) . '/tests/_drafts/test-cache.db4', 'db4', 'c', false
      );
    } catch(RuntimeException $e) {
     $this->markTestSkipped($e->getMessage());
    }

    $this->assertInstanceOf('Cache', $cache);

    $cache->put($this->_identifier, $this->_object);

    $this->assertInstanceOf('stdClass', $cache->get($this->_identifier));
  }

  /**
   * @depends CacheHandlersTest::testOracleBerkeleyDb4HandlerSupportWithoutPersistantConnection
   */
  public function testOracleBerkeleyDb4HandlerBeSupportedWithPersistantConnection()
  {
    try {
      $cache = new Cache(dirname(dirname(__FILE__)) . '/tests/_drafts/test-cache.db4', 'db4');
    } catch(RuntimeException $e) {
     $this->markTestSkipped($e->getMessage());
    }

    $this->assertInstanceOf('Cache', $cache);

    $cache->put($this->_identifier, $this->_object);

    $this->assertInstanceOf('stdClass', $cache->get($this->_identifier));
  }

  /**
   * Tests support for CDB - Tiny Constant Database.
   */
  public function testCanCdbHandlerOnlyNewAndReadBeSupportedWithPersistantConnection()
  {
    $path = dirname(dirname(__FILE__)) . '/tests/_drafts/test-cache-cdb2.cdb';

    // create handler to write.
    try {
      $cacheMake = new Cache($path, 'cdb_make', 'n');
    } catch(RuntimeException $e) {
     $this->markTestSkipped($e->getMessage());
    }

    $this->assertInstanceOf('Cache', $cacheMake);

    $this->assertTrue($cacheMake->put(md5('test123'), $this->_object));

    // for read we close the handler.
    $cacheMake->closeDba();

    // create handler to read.
    try {
      $cacheRead = new Cache($path, 'cdb', 'r');
    } catch(RuntimeException $e) {
     $this->markTestSkipped($e->getMessage());
    }

    $this->assertTrue($cacheRead->has(md5('test123')));

    $this->assertInstanceOf('stdClass', $cacheRead->get(md5('test123')));

    $cacheRead->closeDba();
  }

  public function testCreateAnCdbHandler()
  {
  $path = dirname(dirname(__FILE__)) . '/tests/_drafts/simple-xml-test-cache-on-cdb.db';

    try {
      new Cache(
        $path, "cdb", "n"
      );
      unlink($path);
    } catch(RuntimeException $e) {
     $this->markTestSkipped($e->getMessage());
    }
  }

  public function testPutTheSameIdentifierTwiceToCdbHandler()
  {
    $path = dirname(dirname(__FILE__)) . '/tests/_drafts/test-cache-cdb2.cdb';

    // CREATE HANDLER TO WRITE.
    try {
      $cacheMake = new Cache($path, 'cdb_make', 'n');
    } catch(RuntimeException $e) {
     $this->markTestSkipped($e->getMessage());
    }

    // first insert.
    $this->assertTrue($cacheMake->put('key', 'data'));

    // replace instead of insert.
    $this->assertTrue($cacheMake->put('key', 'data-2'));

    // for read we close the handler.
    $cacheMake->closeDba();

    // CREATE HANDLER TO READ.
    try {
      $cacheRead = new Cache($path, 'cdb', 'r');
    } catch(RuntimeException $e) {
     $this->markTestSkipped($e->getMessage());
    }

    //check if data replaced.
    $this->assertEquals('data', $cacheRead->get('key'));

    $cacheRead->closeDba();
  }
}
