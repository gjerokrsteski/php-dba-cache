<?php

namespace PhpDbaCache\Tests;

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'TestCase.php';

class CdbWriteTest extends CacheHandlersTestCase
{
    public function testFetchingAllIds()
    {
        $this->markTestSkipped('sorry - this handler is not able to read');
    }

    public function testFetchingMetaData()
    {
        $this->markTestSkipped('sorry - this handler is not able to read');
    }

    public function testWithPersistentConnection()
    {
        $path = dirname(dirname(dirname(__FILE__))) . '/tests/_drafts/test-cache-serialised.cdb';

        // create handler to write.
        try {
            $cacheMake = new \PhpDbaCache\Cache($path, 'cdb_make', 'n');
        } catch (\RuntimeException $e) {
            $this->markTestSkipped($e->getMessage());
        }

        $this->assertInstanceOf('\PhpDbaCache\Cache', $cacheMake);

        $this->assertTrue($cacheMake->put(md5('test123'), $this->testObject));

        // for read we close the handler.
        $cacheMake->closeDba();
    }

    public function testPutSameIdentifierTwiceTime()
    {
        $path = dirname(dirname(dirname(__FILE__))) . '/tests/_drafts/test-cache-keyed.cdb';

        try {
            $cacheMake = new \PhpDbaCache\Cache($path, 'cdb_make', 'n');
        } catch (\RuntimeException $e) {
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
