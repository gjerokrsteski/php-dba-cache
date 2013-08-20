<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR .'TestCase.php';

class Db4Test extends CacheHandlersTestCase
{
  public function testWithoutPersistentConnection()
  {
    try {
      $cache = new Cache(
        dirname(dirname(dirname(__FILE__))) . '/tests/_drafts/test-cache.db4', 'db4', 'c', false
      );
    } catch(RuntimeException $e) {
     $this->markTestSkipped($e->getMessage());
    }

    $this->assertInstanceOf('Cache', $cache);

    $cache->put($this->_identifier, $this->_object);

    $this->assertInstanceOf('stdClass', $cache->get($this->_identifier));

    $cache->closeDba();
  }

  /**
   * @depends Db4Test::testWithoutPersistentConnection
   */
  public function testOracleBerkeleyDb4HandlerBeSupportedWithPersistantConnection()
  {
    try {
      $cache = new Cache(dirname(dirname(dirname(__FILE__))) . '/tests/_drafts/test-cache.db4', 'db4');
    } catch(RuntimeException $e) {
     $this->markTestSkipped($e->getMessage());
    }

    $this->assertInstanceOf('Cache', $cache);

    $cache->put($this->_identifier, $this->_object);

    $this->assertInstanceOf('stdClass', $cache->get($this->_identifier));

    $cache->closeDba();
  }
}
