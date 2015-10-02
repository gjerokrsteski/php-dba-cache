<?php

namespace PhpDbaCache\Tests;

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'TestCase.php';

class Db4Test extends CacheHandlersTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->_general_file = dirname(dirname(dirname(__FILE__))) . '/tests/_drafts/test-cache.db4';
        $this->_general_handler = 'db4';
    }

    public function testWithoutPersistentConnection()
    {
        try {
            $cache = new \PhpDbaCache\Cache($this->_general_file, $this->_general_handler, 'c', false);
        } catch (\RuntimeException $e) {
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
            $cache = new \PhpDbaCache\Cache($this->_general_file, $this->_general_handler);
        } catch (\RuntimeException $e) {
            $this->markTestSkipped($e->getMessage());
        }

        $this->assertInstanceOf('Cache', $cache);

        $cache->put($this->_identifier, $this->_object);

        $this->assertInstanceOf('stdClass', $cache->get($this->_identifier));

        $cache->closeDba();
    }
}
