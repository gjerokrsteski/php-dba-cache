<?php
require_once dirname(__FILE__) .'/DummyFixtures.php';


class CacheTest extends PHPUnit_Framework_TestCase
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

    try {
      $this->_cache = new Cache(dirname(dirname(__FILE__)) . '/tests/_drafts/cache.flat');
    } catch(RuntimeException $e) {
     $this->markTestSkipped($e->getMessage());
    }
  }

  /**
   * Cleans up the environment after running a test.
   */
  protected function tearDown()
  {
    unset($this->_cache);

    parent::tearDown();
  }

  public function objectsProvider()
  {
    $stdClass        = new stdClass();
    $stdClass->title = 'Zweiundvierz';
    $stdClass->from  = 'Joe';
    $stdClass->to    = 'Jane';
    $stdClass->body  = 'Ich kenne die Antwort -- aber was ist die Frage?';

    return array(
      array(
        md5('stdClass'),
        $stdClass
      ),
      array(
        md5('ZipArchive'),
        new ZipArchive()
      ),
      array(
        md5('XMLReader'),
        new XMLReader()
      ),
      array(
        md5('Dummy'),
        new Dummy()
      )
    );
  }

  /**
   * @dataProvider objectsProvider
   */
  public function testPutSomeObjectsIntoTheCache($identifier, $object)
  {
    try {
      $this->_cache->put($identifier, $object);
    } catch (Exception $e) {
      $this->fail(
        $e->getMessage()
      );
    }
  }

  /**
   * @depends CacheTest::testPutSomeObjectsIntoTheCache
   * @dataProvider objectsProvider
   */
  public function testGetSomeObjectsFromTheCacheAndCompareEachother($identifier, $expectedObject)
  {
    try {
      $this->assertTrue($this->_cache->has($identifier));

      $getObject = $this->_cache->get($identifier);

      $this->assertEquals($expectedObject, $getObject);
    } catch (Exception $e) {
      $this->fail(
        $e->getMessage()
      );
    }
  }

  public function testReadAllIdsInCache()
  {
    $path = dirname(dirname(__FILE__)) . '/tests/_drafts/test-cache-get-all-ids.flat';

    try {
      $cache = new Cache(
        $path, 'flatfile', 'c', false
      );
    } catch(RuntimeException $e) {
     $this->markTestSkipped($e->getMessage());
    }

    $cache->put('array-1', array( 1 ));
    $cache->put('string-2', 'some big string');
    $cache->put('float-3', 1234.87987698);

    $ids = $cache->getIds();

    $this->assertInstanceOf('ArrayObject', $ids);
    $this->assertEquals($ids[0], 'array-1');
    $this->assertEquals($ids[1], 'string-2');
    $this->assertEquals($ids[2], 'float-3');

    unlink($path);
  }

  public function testPutTheSameIdentifierTwiceToFlatfileHandler()
  {
    $path  = dirname(dirname(__FILE__)) . '/tests/_drafts/test-cache-insert.flat';

    try {
      $cache = new Cache($path, 'flatfile', 'c', false);
    } catch(RuntimeException $e) {
     $this->markTestSkipped($e->getMessage());
    }

    // first insert.
    $this->assertTrue($cache->put('key', 'data'));

    // replace instead of insert.
    $this->assertTrue($cache->put('key', 'data-2'));

    //check if data replaced.
    $this->assertEquals('data-2', $cache->get('key'));
  }

  public function testPutTheSameIdentifierTwiceToDb4Handler()
  {
    $path = dirname(dirname(__FILE__)) . '/tests/_drafts/test-cache.db4';

    try {
      $cache = new Cache($path, 'db4');
    } catch(RuntimeException $e) {
     $this->markTestSkipped($e->getMessage());
    }

    // first insert.
    $this->assertTrue($cache->put('key', 'data'));

    // replace instead of insert.
    $this->assertTrue($cache->put('key', 'data-2'));

    //check if data replaced.
    $this->assertEquals('data-2', $cache->get('key'));
  }

  public function testLoadingMetadata()
  {
    $path  = dirname(dirname(__FILE__)) . '/tests/_drafts/test-cache.db4';

    try {
      $cache = new Cache($path, 'db4');
    } catch(RuntimeException $e) {
     $this->markTestSkipped($e->getMessage());
    }

    // first insert.
    $this->assertTrue($cache->put('key', 'data'));

    $this->assertInternalType('array', $cache->getMetaData('key'));
    $this->assertNotEmpty($cache->getMetaData('key'));
  }
}
