<?php
namespace PhpDbaCache\Tets;

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'TestCase.php';

class CdbReadTest extends \PhpDbaCache\Tests\CacheHandlersTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->generalCacheFile = dirname(dirname(dirname(__FILE__))) . '/tests/_drafts/test-cache-serialised.cdb';
        $this->generalHandler = 'cdb';
        $this->generalMode = 'r';
        $this->identifier = md5('test123');
    }

    public function testPuttingForever()
    {
        $this->markTestSkipped('sorry - this handler is not able to write');
    }


    public function testIfNoDatabaseExists()
    {
        $path = dirname(dirname(dirname(__FILE__))) . '/tests/_drafts/simple-xml-test-cache-on-cdb.db';
        try {
            new \PhpDbaCache\Cache(
                $path, "cdb", "n"
            );
            @unlink($path);
        } catch (\RuntimeException $e) {
            $this->markTestSkipped($e->getMessage());
        }
    }

    public function testWithPersistentConnection()
    {
        $path = dirname(dirname(dirname(__FILE__))) . '/tests/_drafts/test-cache-serialised.cdb';

        // create handler to read.
        try {
            $cacheRead = new \PhpDbaCache\Cache($path, 'cdb', 'r');
        } catch (\RuntimeException $e) {
            $this->markTestSkipped($e->getMessage());
        }

        $this->assertTrue($cacheRead->has(md5('test123')));

        $this->assertInstanceOf('\PhpDbaCache\stdClass', $cacheRead->get(md5('test123')));

        $cacheRead->closeDba();
    }

    public function testSameIdentifierTwiceTime()
    {
        $path = dirname(dirname(dirname(__FILE__))) . '/tests/_drafts/test-cache-keyed.cdb';

        try {
            $cacheRead = new \PhpDbaCache\Cache($path, 'cdb', 'r');
        } catch (\RuntimeException $e) {
            $this->markTestSkipped($e->getMessage());
        }

        //check if data replaced.
        $this->assertEquals('data', $cacheRead->get('key'));

        $cacheRead->closeDba();
    }
}
