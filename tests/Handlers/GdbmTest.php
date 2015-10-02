<?php
namespace PhpDbaCache\Tests;

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'TestCase.php';

class GdbmTest extends CacheHandlersTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->_general_file = dirname(dirname(dirname(__FILE__))) . '/tests/_drafts/test-cache.gdbm';
        $this->_general_handler = 'gdbm';
    }

    public function testWriteAndReadWithoutPersistentConnection()
    {
        try {
            $cache = new \PhpDbaCache\Cache(
                dirname(dirname(dirname(__FILE__))) . '/tests/_drafts/test-cache.gdbm', 'gdbm', 'c', false
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
            $cache = new \PhpDbaCache\Cache(dirname(dirname(dirname(__FILE__))) . '/tests/_drafts/test-cache.gdbm', 'gdbm');
        } catch (\RuntimeException $e) {
            $this->markTestSkipped($e->getMessage());
        }

        $this->assertInstanceOf('\PhpDbaCache\Cache', $cache);

        $cache->put($this->_identifier, $this->_object);

        $this->assertInstanceOf('\PhpDbaCache\stdClass', $cache->get($this->_identifier));
    }

}
