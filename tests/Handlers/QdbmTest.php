<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR .'TestCase.php';

class QdbmTest extends CacheHandlersTestCase
{
  public function testWriteAndReadWithoutPersistentConnection()
  {
    try {
      $cache = new Cache(
        dirname(dirname(dirname(__FILE__))) . '/tests/_drafts/test-cache.qdbm', 'qdbm', 'c', false
      );
    } catch(RuntimeException $e) {
     $this->markTestSkipped($e->getMessage());
    }

    $this->assertInstanceOf('Cache', $cache);

    $cache->put($this->_identifier, $this->_object);

    $this->assertInstanceOf('stdClass', $cache->get($this->_identifier));
  }

  /**
   * @depends CacheHandlersTest::testSupportWithoutPersistentConnection
   */
  public function testWriteAndReaddWithPersistentConnection()
  {
    try {
      $cache = new Cache(dirname(dirname(dirname(__FILE__))) . '/tests/_drafts/test-cache.qdbm', 'qdbm');
    } catch(RuntimeException $e) {
     $this->markTestSkipped($e->getMessage());
    }

    $this->assertInstanceOf('Cache', $cache);

    $cache->put($this->_identifier, $this->_object);

    $this->assertInstanceOf('stdClass', $cache->get($this->_identifier));
  }
}
