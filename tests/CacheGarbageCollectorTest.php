<?php
require_once dirname(__FILE__) .'/DummyFixtures.php';

class CacheGarbageCollectorTest extends PHPUnit_Framework_TestCase
{
  /**
   * @var Cache
   */
  private $_cache;

  /**
   * Prepares the environment before running a test.
   */
  protected function setUp()
  {
    parent::setUp();

    $path = dirname(dirname(__FILE__)) . '/tests/_drafts/garbage-collection-test-cache.inifile';

    try {
      $this->_cache = new Cache($path, 'inifile');
    } catch(RuntimeException $e) {
      $this->markTestSkipped($e->getMessage());
    }
  }

  /**
   * Cleans up the environment after running a test.
   */
  protected function tearDown()
  {
	if ($this->_cache) {
	   $this->_cache->closeDba();
	}
    parent::tearDown();
  }

  public function testCreatingGarbageCollectionObject()
  {
    $garbageCollection = new Sweeper($this->_cache);

    $this->assertInstanceOf('Sweeper', $garbageCollection);
  }

  /**
   * @depends CacheGarbageCollectorTest::testCreatingGarbageCollectionObject
   */
  public function testCleanAllFromTheGarbageCollection()
  {
    $dba = $this->_cache->getDba();

    // prepare data.
    $stdClass        = new stdClass();
    $stdClass->title = 'Hi firend, i am cached.';
    $stdClass->from  = 'Joe';
    $stdClass->to    = 'Hover';
    $stdClass->body  = 'Yes, it works!';

    // put some data to the cache.
    $this->_cache->put(md5('stdClass'), $stdClass, 1);
    $this->_cache->put(md5('ZipArchive'), new ZipArchive(), 1);
    $this->_cache->put(md5('XMLReader'), new XMLReader(), 1);


    sleep(1);

    $garbageCollection = new Sweeper($this->_cache);
    $garbageCollection->cleanAll();

    $this->assertFalse($this->_cache->get(md5('stdClass')));

    $this->assertFalse($this->_cache->get(md5('ZipArchive')));

    $this->assertFalse($this->_cache->get(md5('XMLReader')));
  }

  /**
   * @depends CacheGarbageCollectorTest::testCreatingGarbageCollectionObject
   */
  public function testCleanTheGarbageCollectionByNotSuitableExpirationTime()
  {
    // prepare data.
    $stdClass        = new stdClass();
    $stdClass->title = 'I am cached.';
    $stdClass->from  = 'Mike';
    $stdClass->to    = 'Gates';
    $stdClass->body  = 'Yes, it works fine!';

    // put some data to the cache.
    $this->_cache->put(md5('stdClass'), $stdClass);
    $this->_cache->put(md5('ZipArchive'), new ZipArchive());
    $this->_cache->put(md5('XMLReader'), new XMLReader());

    // wait one second to force the expiration-time-calculation.
    sleep(1);

    $garbageCollection = new Sweeper($this->_cache);
    $garbageCollection->cleanOld();

    $this->assertInstanceOf('stdClass', $this->_cache->get(md5('stdClass')));
    $this->assertInstanceOf('ZipArchive', $this->_cache->get(md5('ZipArchive')));
    $this->assertInstanceOf('XMLReader', $this->_cache->get(md5('XMLReader')));
  }

  /**
   * Tests support for CDB - Tiny Constant Database.
   * CDB can not be deleted - clear garbage manually.
   */
  public function testCleanTheGarbageCollectionWithCdbHandler()
  {
    $path = dirname(dirname(__FILE__)) . '/tests/_drafts/test-cache-cdb2.cdb';

    // create cdb-handler to write.
    try {
      $cacheMake = new Cache($path, 'cdb_make', 'n');
    } catch(RuntimeException $e) {
     $this->markTestSkipped($e->getMessage());
    }

    $this->assertInstanceOf('Cache', $cacheMake);

    $testIdentifier1 = md5('ZipArchive' . time());
    $testIdentifier2 = md5('XMLReader' . time());

    $this->assertTrue($cacheMake->put($testIdentifier1, new ZipArchive()));
    $this->assertTrue($cacheMake->put($testIdentifier2, new XMLReader()));

    // CacheGarbageCollector has no effect.
    $garbageCollection = new Sweeper($cacheMake);
    $garbageCollection->cleanAll();

    // deleting has no effect.
    $cacheMake->delete($testIdentifier1);
    $cacheMake->delete($testIdentifier2);

    // for read we close the handler.
    $cacheMake->closeDba();

    // create cdb-handler to read.
    try {
      $cacheRead = new Cache($path, 'cdb', 'r');
    } catch(RuntimeException $e) {
     $this->markTestSkipped($e->getMessage());
    }

    $this->assertTrue($cacheRead->has($testIdentifier1));
    $this->assertTrue($cacheRead->has($testIdentifier2));

    $this->assertInstanceOf('ZipArchive', $cacheRead->get($testIdentifier1));
    $this->assertInstanceOf('XMLReader', $cacheRead->get($testIdentifier2));

    $cacheRead->closeDba();
  }

  /**
   * Tests support for DB4 - Oracle Berkeley DB 4.
   */
  public function testCleanTheGarbageCollectionWithDb4Handler()
  {
    $path = dirname(dirname(__FILE__)) . '/tests/_drafts/test-cache.db4';

    try {
      $cache = new Cache($path, 'db4', 'c', false);
    } catch(RuntimeException $e) {
     $this->markTestSkipped($e->getMessage());
    }

    $this->assertInstanceOf('Cache', $cache);

    $cache->put(md5('ZipArchive'), new ZipArchive());
    $cache->put(md5('XMLReader'), new XMLReader());

    $this->assertInstanceOf('ZipArchive', $cache->get(md5('ZipArchive')));
    $this->assertInstanceOf('XMLReader', $cache->get(md5('XMLReader')));

    $garbageCollection = new Sweeper($cache);
    $garbageCollection->cleanAll();

    $this->assertFalse($cache->get(md5('ZipArchive')));
    $this->assertFalse($cache->get(md5('XMLReader')));

    $cache->closeDba();

    unlink($path);
  }

  public function testUtilMethods()
  {
    $garbageCollection = new Sweeper($this->_cache);

    $this->assertTrue($garbageCollection->flush());
  }
}
