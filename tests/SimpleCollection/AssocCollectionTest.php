<?php

namespace Tests\SimpleCollection;

use SimpleCollection\AbstractCollection;
use SimpleCollection\ArrayCollection;
use SimpleCollection\AssocCollection;

/**
 * AssocCollection Test
 *
 * @copyright Felix Buchheim
 * @author    Felix Buchheim <hanibal4nothing@gmail.com>
 */
class AssocCollectionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Object to test
     *
     * @var AssocCollection
     */
    protected $object;

    /**
     * Set the object to test
     */
    public function setUp()
    {
        parent::setUp();
        $this->object = new AssocCollection();
    }

    /**
     * TestCase for empty Constructor
     */
    public function testEmptyConstructor()
    {
        $this->assertEmpty($this->object);
    }

    /**
     * TestConstructor
     *
     * @param array $aValues
     * @param int   $iAssertCount
     *
     * @dataProvider constructProvider
     */
    public function testConstruct(array $aValues, $iAssertCount)
    {
        $this->object = new ArrayCollection($aValues);
        $this->assertCount($iAssertCount, $this->object);
    }

    /**
     * Check to reset keys on construct
     *
     * @param array $aValues
     * @param int   $iValueCount
     *
     * @dataProvider constructProvider
     */
    public function testResetKeys(array $aValues, $iValueCount)
    {
        $this->object = new ArrayCollection($aValues);
        for ($i = 0; $i < $iValueCount; $i++) {
            $this->assertTrue($this->object->offsetExists($i));
        }
        $this->assertFalse($this->object->offsetExists($iValueCount + 1));
    }

    /**
     * Test current function
     */
    public function testCurrent()
    {
        $this->assertNull($this->object->current());

        $this->object->offsetSet(0, 42);
        $this->assertEquals(42, $this->object->current());

        $this->object->offsetSet(1, 24);
        $this->assertEquals(42, $this->object->current());

        $this->assertEquals(24, $this->object->next());
        $this->assertEquals(24, $this->object->current());
    }

    /**
     * Test is valid
     */
    public function testValid()
    {
        $this->assertFalse($this->object->valid());

        $this->object->offsetSet(0, 42);
        $this->assertTrue($this->object->valid());

        $this->assertFalse($this->object->next());
        $this->assertFalse($this->object->valid());
    }

    /**
     * Test next and prev
     */
    public function testNextAndPrev()
    {
        $this->assertFalse($this->object->next());
        $this->assertFalse($this->object->prev());

        $this->object->set(array(1, 2, 3));

        $this->assertEquals(1, $this->object->current());
        $this->assertFalse($this->object->prev());
        $this->assertEquals(1, $this->object->rewind());
        $this->assertEquals(2, $this->object->next());

        $this->assertEquals(1, $this->object->rewind());
        $this->assertEquals(2, $this->object->next());
        $this->assertEquals(3, $this->object->next());

        $this->assertFalse($this->object->next());
        $this->assertEquals(AbstractCollection::NOT_SET_FLAG, $this->object->scNext());
    }

    /***
     * Test scNext and scPrev
     */
    public function testScNextAndPrev()
    {
        $this->assertEquals(AbstractCollection::NOT_SET_FLAG, $this->object->scNext());
        $this->assertEquals(AbstractCollection::NOT_SET_FLAG, $this->object->scPrev());

        $this->object->set(array(1, false, true, 0, null, ''));

        $this->assertEquals(AbstractCollection::NOT_SET_FLAG, $this->object->scPrev());

        $this->assertEquals(1, $this->object->rewind());
        $this->assertFalse($this->object->scNext());

        $this->assertEquals(1, $this->object->scPrev());
        $this->assertFalse($this->object->scNext());

        $this->assertTrue($this->object->scNext());
        $this->assertFalse($this->object->scPrev());

        $this->assertTrue($this->object->scNext());
        $this->assertEquals(0, $this->object->scNext());
        $this->assertTrue($this->object->scPrev());

        $this->assertEquals(0, $this->object->scNext());
        $this->assertNull($this->object->scNext());

        $this->assertEquals('', $this->object->scNext());
        $this->assertNull($this->object->scPrev());

        $this->assertEquals('', $this->object->scNext());
        $this->assertEquals(AbstractCollection::NOT_SET_FLAG, $this->object->scNext());
    }

    /**
     * Test key
     */
    public function testKey()
    {
        $this->assertNull($this->object->key());

        $this->object->set(array(1, 2, false, null));

        $this->assertEquals(0, $this->object->key());
        $this->assertNull($this->object->end());

        $this->assertEquals(3, $this->object->key());
    }

    /**
     * Test if offsetExists in collection
     */
    public function testOffsetExists()
    {
        $this->assertFalse($this->object->offsetExists(0));

        $this->object->set(array(1, 2, 3, 42, 'x'));

        $this->assertTrue($this->object->offsetExists(0));
        $this->assertTrue($this->object->offsetExists(1));
        $this->assertTrue($this->object->offsetExists(2));
        $this->assertTrue($this->object->offsetExists(3));
        $this->assertTrue($this->object->offsetExists(4));
        $this->assertFalse($this->object->offsetExists(5));
    }

    /**
     * Test offsetGet
     */
    public function testOffsetGet()
    {
        $this->object->set(array(1, 2.0, 42, 'x'));

        $this->assertEquals(1, $this->object->offsetGet(0));
        $this->assertEquals(2.0, $this->object->offsetGet(1));
        $this->assertEquals(42, $this->object->offsetGet(2));
        $this->assertEquals('x', $this->object->offsetGet(3));
    }

    /**
     * Test get
     */
    public function testGet()
    {
        $this->assertNull($this->object->get(0));
        $this->assertEquals(42, $this->object->get(0, 42));

        $this->object->offsetSet(3, '42');
        $this->assertEquals('42', $this->object->get(3));
        $this->assertEquals('42', $this->object->get(3, 44));
    }

    /**
     * Test to set value by offset
     */
    public function testOffsetSet()
    {
        $this->assertInstanceOf(get_class($this->object), $this->object->offsetSet(12, 42));
        $this->assertEquals(42, $this->object->get(12));
        $this->assertInstanceOf(get_class($this->object), $this->object->offsetSet(12, 'x'));
        $this->assertEquals('x', $this->object->get(12));

        $this->assertInstanceOf(get_class($this->object), $this->object->offsetSet('', 'y'));
        $this->assertEquals('y', $this->object->get(''));
    }

    /**
     * Test exception on key = null
     *
     * @expectedException \InvalidArgumentException
     * @dataProvider offsetSetExceptionProvider
     *
     * @param $mOffset
     */
    public function testOffsetSetException($mOffset)
    {
        $this->object->offsetSet($mOffset, 42);
    }

    /**
     * Test to unset value by offset
     */
    public function testOffsetUnset()
    {
        $this->assertCount(0, $this->object);
        $this->object->set(array('a', 'b', 'c'));

        $this->assertInstanceOf(get_class($this->object), $this->object->offsetUnset(0));
        $this->assertInstanceOf(get_class($this->object), $this->object->offsetUnset(2));

        $this->assertNull($this->object->get(0));
        $this->assertNull($this->object->get(2));
        $this->assertEquals('b', $this->object->get(1));
    }

    /**
     * Test to seek the pointer to given offset
     */
    public function testSeek()
    {
        $this->object->set(array(1, 2, 3, 4, 5, 'x'));
        $this->assertEquals(3, $this->object->seek(2));
        $this->assertEquals(2, $this->object->prev());
        $this->assertEquals(4, $this->object->seek(3));
        $this->assertEquals(5, $this->object->next());
        $this->assertEquals('x', $this->object->seek(5));
    }

    /**
     * Test seek to given key
     */
    public function seekToKey()
    {
        $this->object->set(array('a', 'b', 'c', 'd'));

        $this->assertEquals('b', $this->object->seekToKey(1));
        $this->assertEquals('b', $this->object->seekToKey('1'));
        $this->assertEquals('a', $this->object->prev());

        $this->assertEquals('d', $this->object->seekToKey(3));
        $this->assertEquals('d', $this->object->seekToKey('3'));
        $this->assertEquals('c', $this->object->prev());
    }

    /**
     * Test to throw an exception if seek is bigger then offset
     *
     * @expectedException  \OutOfBoundsException
     * @expectedExceptionMessage Invalid seek position: 6
     */
    public function testOutOfBoundsSeekException()
    {
        $this->object->set(array(1, 2, 3, 4, 5, 'x'));
        $this->object->seek(6);
    }

    /**
     * Test to throw an exception if seek is bigger then offset
     *
     * @expectedException  \OutOfBoundsException
     * @expectedExceptionMessage Invalid seek position: 5
     */
    public function testOutOfBoundsSeekToKeyException()
    {
        $this->object->set(array(1, 2, 3, 4, 5, 'x'));
        $this->object->seekToKey('5', true);
    }

    /**
     * Test the isEmpty function
     */
    public function testIsEmpty()
    {
        $this->assertTrue($this->object->isEmpty());
        $this->object->set(array(42));
        $this->assertFalse($this->object->isEmpty());

        $this->object->offsetUnset(0);
        $this->assertTrue($this->object->isEmpty());
    }

    /**
     * Test to get all values as array from collection
     */
    public function testGetAll()
    {
        $this->assertEquals(array(), $this->object->getAll());
        $aArray = array(1, 2, 3);
        $this->object->set($aArray);
        $this->assertEquals($aArray, $this->object->getAll());

        $this->object->offsetSet(3, 4);
        $this->assertEquals(array(1, 2, 3, 4), $this->object->getAll());
    }

    /**
     * DataProvider for testOffsetSetException
     *
     * @return array
     */
    public function offsetSetExceptionProvider()
    {
        $aTestCases = array();

        $aTestCases['null']   = array('mOffset' => null);
        $aTestCases['float']  = array('mOffset' => 2.5);
        $aTestCases['bool']   = array('mOffset' => false);
        $aTestCases['object'] = array('mOffset' => new \stdClass());

        return $aTestCases;
    }

    /**
     * DataProvider for testConstruct
     *
     * @return array
     */
    public function constructProvider()
    {
        $aTestCases                   = array();
        $aTestCases['emptyArray']     = array(
            'aValues'      => array(),
            'iAssertCount' => 0
        );
        $aTestCases['oneValue']       = array(
            'aValues'      => array(42),
            'iAssertCount' => 1
        );
        $aTestCases['multipleValues'] = array(
            'aValues'      => array(42, 'x', 'abc'),
            'iAssertCount' => 3
        );
        $aTestCases['indexValues']    = array(
            'aValues'      => array('a' => 42, 2 => 'x', '3' => 'abc'),
            'iAssertCount' => 3
        );

        return $aTestCases;
    }

}