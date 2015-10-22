<?php

namespace PhpDbaCache\Tests;

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'TestCase.php';

class Db4Test extends CacheHandlersTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->generalCacheFile = dirname(dirname(dirname(__FILE__))) . '/tests/_drafts/test-cache.db4';
        $this->generalHandler = 'db4';
    }

    public function testWithoutPersistentConnection()
    {
        try {
            $cache = new \PhpDbaCache\Cache($this->generalCacheFile, $this->generalHandler, 'c', false);
        } catch (\RuntimeException $e) {
            $this->markTestSkipped($e->getMessage());
        }

        $this->assertInstanceOf('\PhpDbaCache\Cache', $cache);

        $cache->put($this->identifier, $this->testObject);

        $this->assertInstanceOf('\stdClass', $cache->get($this->identifier));

        $cache->closeDba();
    }

    /**
     * @depends Db4Test::testWithoutPersistentConnection
     */
    public function testOracleBerkeleyDb4HandlerBeSupportedWithPersistantConnection()
    {
        try {
            $cache = new \PhpDbaCache\Cache($this->generalCacheFile, $this->generalHandler);
        } catch (\RuntimeException $e) {
            $this->markTestSkipped($e->getMessage());
        }

        $this->assertInstanceOf('\PhpDbaCache\Cache', $cache);

        $cache->put($this->identifier, $this->testObject);

        $this->assertInstanceOf('\stdClass', $cache->get($this->identifier));

        $cache->closeDba();
    }
}
