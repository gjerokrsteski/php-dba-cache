<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR .'TestCase.php';

class FlatfileTest extends CacheHandlersTestCase
{
  public function testWriteAndReadData()
  {
    try {
      $cache = new Cache(
        dirname(dirname(dirname(__FILE__))) . '/tests/_drafts/test-cache.flatfile'
      );
    } catch(RuntimeException $e) {
     $this->markTestSkipped($e->getMessage());
    }

    $this->assertInstanceOf('Cache', $cache);

    $cache->put($this->_identifier, $this->_object);

    $this->assertInstanceOf('stdClass', $cache->get($this->_identifier));
  }
}
