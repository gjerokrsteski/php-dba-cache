<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR .'TestCase.php';

class FlatfileTest extends CacheHandlersTestCase
{
  protected function setUp()
  {
    parent::setUp();

    $this->_general_file    = dirname(dirname(dirname(__FILE__))) . '/tests/_drafts/test-cache.flatfile';
    $this->_general_handler = 'flatfile';
    $this->_general_mode    = 'c-';
  }

  public function testWriteAndReadWithoutPersistentConnection()
  {
    try {
      $cache = new Cache(
        dirname(dirname(dirname(__FILE__))) . '/tests/_drafts/test-cache-pers.flatfile', 'flatfile', 'c-', false
      );
    } catch(RuntimeException $e) {
     $this->markTestSkipped($e->getMessage());
    }

    $this->assertInstanceOf('Cache', $cache);

    $cache->put($this->_identifier, array('rambo' => 123));

    $res = $cache->get($this->_identifier);

    $this->assertInternalType('array', $res);
    $this->assertEquals($res, array('rambo' => 123));
  }
}
