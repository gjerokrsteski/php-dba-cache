<?php
namespace PhpDbaCache\Tests;

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'DummyFixtures.php';

class CacheHandlersTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \stdClass
     */
    protected $testObject;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var string
     */
    protected $generalCacheFile = 'flatfile.db';

    /**
     * @var string
     */
    protected $generalHandler = 'flatfile';

    /**
     * @var string
     */
    protected $generalMode = 'c';

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $stdClass = new \stdClass();
        $stdClass->title = 'Zweiundvierz';
        $stdClass->from = 'Joe';
        $stdClass->to = 'Jane';
        $stdClass->body = new \Fixtures\Dummy();

        $this->testObject = $stdClass;
        $this->identifier = md5('stdClass' . time());
        $this->generalCacheFile = dirname(dirname(dirname(__FILE__))) . '/tests/_drafts/flatfile.db';
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        unset($this->testObject, $this->identifier);
        parent::tearDown();
    }

    public function testPuttingForever()
    {
        try {
            $cache = new \PhpDbaCache\Cache($this->generalCacheFile, $this->generalHandler, $this->generalMode);
        } catch (\RuntimeException $e) {
            $this->markTestSkipped($e->getMessage());
        }

        $cache->forever('forever', array('forever' => 123));

        $res = $cache->getIds();

        $this->assertInstanceOf('\ArrayObject', $res, 'no instance of ArrayObject');
        $this->assertNotEmpty($res->getArrayCopy());

        $cache->closeDba();
    }

    public function badHandlersProvider()
    {
        return array(
            array('bad-bad-handler'),
            array(123),
            array(1),
            array('0'),
            array(' '),
            array(null),
            array(true),
            array(false),
        );
    }

    /**
     * @expectedException \RuntimeException
     * @dataProvider badHandlersProvider
     */
    public function testIfBadHandlerGiven()
    {
        new \PhpDbaCache\Cache($this->generalCacheFile, 'bad-bad-handler');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testIfBadDbFileGiven()
    {
        new \PhpDbaCache\Cache('/path/to/bad-bad-file.db', $this->generalHandler, 'r');
    }

}
