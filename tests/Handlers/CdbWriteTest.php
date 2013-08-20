<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR .'TestCase.php';

class CdbWriteTest extends CacheHandlersTestCase
{
  public function testWithPersistentConnection()
  {
    $path = dirname(dirname(dirname(__FILE__))) . '/tests/_drafts/test-cache-serialised.cdb';

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
  }

  public function testPutSameIdentifierTwiceTime()
  {
    $path = dirname(dirname(dirname(__FILE__))) . '/tests/_drafts/test-cache-keyed.cdb';

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
  }
}
