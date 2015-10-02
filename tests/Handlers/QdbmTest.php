<?php

namespace PhpDbaCache\Tests;

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'TestCase.php';

class QdbmTest extends CacheHandlersTestCase
{

    protected function setUp()
    {
        parent::setUp();

        $this->_general_file = dirname(dirname(dirname(__FILE__))) . '/tests/_drafts/test-cache.qdbm';
        $this->_general_handler = 'qdbm';
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

        $cache->put($this->_identifier, $this->_object);

        $this->assertInstanceOf('\PhpDbaCache\stdClass', $cache->get($this->_identifier));
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

        $cache->put($this->_identifier, $this->_object);

        $this->assertInstanceOf('\PhpDbaCache\stdClass', $cache->get($this->_identifier));
    }
}
