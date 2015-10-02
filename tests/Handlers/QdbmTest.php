<?php

namespace PhpDbaCache\Tests;

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'TestCase.php';

class QdbmTest extends CacheHandlersTestCase
{

    protected function setUp()
    {
        parent::setUp();

        $this->generalCacheFile = dirname(dirname(dirname(__FILE__))) . '/tests/_drafts/test-cache.qdbm';
        $this->generalHandler = 'qdbm';
    }

    public function testWriteAndReadWithoutPersistentConnection()
    {
        try {
            $cache = new \PhpDbaCache\Cache(
                dirname(dirname(dirname(__FILE__))) . '/tests/_drafts/test-cache.qdbm', 'qdbm', 'c', false
            );
        } catch (\RuntimeException $e) {
            $this->markTestSkipped($e->getMessage());
        }

        $this->assertInstanceOf('\PhpDbaCache\Cache', $cache);

        $cache->put($this->identifier, $this->testObject);

        $this->assertInstanceOf('\PhpDbaCache\stdClass', $cache->get($this->identifier));
    }

    /**
     * @depends CacheHandlersTest::testSupportWithoutPersistentConnection
     */
    public function testWriteAndReaddWithPersistentConnection()
    {
        try {
            $cache = new \PhpDbaCache\Cache(dirname(dirname(dirname(__FILE__))) . '/tests/_drafts/test-cache.qdbm', 'qdbm');
        } catch (\RuntimeException $e) {
            $this->markTestSkipped($e->getMessage());
        }

        $this->assertInstanceOf('\PhpDbaCache\Cache', $cache);

        $cache->put($this->identifier, $this->testObject);

        $this->assertInstanceOf('\PhpDbaCache\stdClass', $cache->get($this->identifier));
    }
}
