<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR .'TestCase.php';

class GdbmTest extends CacheHandlersTestCase
{
  public function testWriteAndReadWithoutPersistentConnection()
  {
    try {
      $cache = new Cache(
        dirname(dirname(dirname(__FILE__))) . '/tests/_drafts/test-cache.gdbm', 'gdbm', 'c', false
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
      $cache = new Cache(dirname(dirname(dirname(__FILE__))) . '/tests/_drafts/test-cache.gdbm', 'gdbm');
    } catch(RuntimeException $e) {
     $this->markTestSkipped($e->getMessage());
    }

    $this->assertInstanceOf('Cache', $cache);

    $cache->put($this->_identifier, $this->_object);

    $this->assertInstanceOf('stdClass', $cache->get($this->_identifier));
  }

}
