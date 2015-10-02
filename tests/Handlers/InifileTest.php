<?php
namespace PhpDbaCache\Tests;

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'TestCase.php';

class InifileTest extends CacheHandlersTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->generalCacheFile = dirname(dirname(dirname(__FILE__))) . '/tests/_drafts/test-cache.inifile';
        $this->generalHandler = 'inifile';
    }

    public function testWriteAndReadWithoutPersistentConnection()
    {
        try {
            $cache = new \PhpDbaCache\Cache(
                dirname(dirname(dirname(__FILE__))) . '/tests/_drafts/test-cache.inifile', 'inifile', 'c', false
            );
        } catch (\RuntimeException $e) {
            $this->markTestSkipped($e->getMessage());
        }

        $this->assertInstanceOf('\PhpDbaCache\Cache', $cache);

        $cache->put($this->identifier, array('rambo' => 123));

        $res = $cache->get($this->identifier);

        $this->assertInternalType('array', $res);
        $this->assertEquals($res, array('rambo' => 123));
    }
}
