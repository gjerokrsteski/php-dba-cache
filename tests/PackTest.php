<?php

namespace PhpDbaCache\Tests;

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'DummyFixtures.php';

class PackTest extends \PHPUnit_Framework_TestCase
{
    public function testCreatingNewObject()
    {
        $this->assertNotNull(new \PhpDbaCache\Pack());
    }

    public function objectsProvider()
    {
        $stdClass = new \stdClass();
        $stdClass->title = 'Zweiundvierz';
        $stdClass->from = 'Joe';
        $stdClass->to = 'Jane';
        $stdClass->body = new \Fixtures\Dummy2();

        return array(
            array(new \Fixtures\Dummy2()),
            array($stdClass),
            array(new \ZipArchive()),
            array(new \XMLReader()),
            array('i am a string'),
            array(123456789),
            array(
                array(
                    'boo' => 1,
                    'foo' => 2,
                    'laa' => 3
                )
            )
        );
    }

    /**
     * @depends      PackTest::testCreatingNewObject
     * @dataProvider objectsProvider
     */
    public function testSerializingSomeObjects($object)
    {
        \PhpDbaCache\Pack::wrap($object);
    }

    /**
     * @depends      PackTest::testCreatingNewObject
     * @depends      PackTest::testSerializingSomeObjects
     * @dataProvider objectsProvider
     */
    public function testUnserializingSomeObjectsAndCompareThemEachOther($object)
    {
        $serialized = \PhpDbaCache\Pack::wrap($object);

        $userItem = \PhpDbaCache\Pack::unwrap($serialized);

        $this->assertEquals($object, $userItem->object);
    }

    public static function provideSerializedTestData()
    {
        return array(
            array(array(21.123, 21.124, 2, 0)),
            array((object)array('eee' => 21.123, 'asdfasdf' => 21.124)),
            array(new \Fixtures\Dummy2()),
            array(new \Fixtures\Dummy1()),
            array(new \Fixtures\Dummy()),
        );
    }

    /**
     * @dataProvider provideSerializedTestData
     */
    public function testIfCanBePackedIn($data)
    {
        $this->assertInternalType(

            'string',

            \PhpDbaCache\Pack::wrap($data),

            'problem on asserting that ' . print_r($data, true) . ' can be serialized'

        );
    }

    public static function provideNotAndBadSerializedTestData()
    {
        return array(
            array(null),
            array(''),
            array('  '),
            array('0'),
            array(0),
            array(true),
            array('O:7:"Capsule":5:1  :4:"type";'),
            array('{s:7:"forever";i:123;'),
            array(array('eee' => 21.123, 'asdfasdf' => 21.124)),
            array(array('eee' => 21.123, 'asdfasdf' => 21.124)),
        );
    }

    /**
     * @dataProvider provideNotAndBadSerializedTestData
     * @expectedException RuntimeException
     */
    public function testFailsIfCanNotBePackedOut($data)
    {
        \PhpDbaCache\Pack::unwrap($data);
    }
}

