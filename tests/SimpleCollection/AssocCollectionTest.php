<?php

namespace Tests\SimpleCollection;

use SimpleCollection\AbstractCollection;
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
        $this->assertCount(0, $this->object);
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

        $immutable = $this->object->update(array(1, 2, 3));

        $this->assertEquals(1, $immutable->current());
        $this->assertFalse($immutable->prev());
        $this->assertEquals(1, $immutable->rewind());
        $this->assertEquals(2, $immutable->next());

        $this->assertEquals(1, $immutable->rewind());
        $this->assertEquals(2, $immutable->next());
        $this->assertEquals(3, $immutable->next());

        $this->assertFalse($immutable->next());
        $this->assertEquals(AbstractCollection::NOT_SET_FLAG, $immutable->scNext());
    }

    /***
     * Test scNext and scPrev
     */
    public function testScNextAndPrev()
    {
        $this->assertEquals(AbstractCollection::NOT_SET_FLAG, $this->object->scNext());
        $this->assertEquals(AbstractCollection::NOT_SET_FLAG, $this->object->scPrev());

        $immutable = $this->object->update(array(1, false, true, 0, null, ''));

        $this->assertEquals(AbstractCollection::NOT_SET_FLAG, $immutable->scPrev());

        $this->assertEquals(1, $immutable->rewind());
        $this->assertFalse($immutable->scNext());

        $this->assertEquals(1, $immutable->scPrev());
        $this->assertFalse($immutable->scNext());

        $this->assertTrue($immutable->scNext());
        $this->assertFalse($immutable->scPrev());

        $this->assertTrue($immutable->scNext());
        $this->assertEquals(0, $immutable->scNext());
        $this->assertTrue($immutable->scPrev());

        $this->assertEquals(0, $immutable->scNext());
        $this->assertNull($immutable->scNext());

        $this->assertEquals('', $immutable->scNext());
        $this->assertNull($immutable->scPrev());

        $this->assertEquals('', $immutable->scNext());
        $this->assertEquals(AbstractCollection::NOT_SET_FLAG, $immutable->scNext());
    }

    /**
     * Test key
     */
    public function testKey()
    {
        $this->assertNull($this->object->key());

        $immutable = $this->object->update(array(1, 2, false, null));

        $this->assertEquals(0, $immutable->key());
        $this->assertNull($immutable->end());

        $this->assertEquals(3, $immutable->key());
    }

    /**
     * Test if offsetExists in collection
     */
    public function testOffsetExists()
    {
        $this->assertFalse($this->object->offsetExists(0));

        $immutable = $this->object->update(array(1, 2, 3, 42, 'x'));

        $this->assertTrue($immutable->offsetExists(0));
        $this->assertTrue($immutable->offsetExists(1));
        $this->assertTrue($immutable->offsetExists(2));
        $this->assertTrue($immutable->offsetExists(3));
        $this->assertTrue($immutable->offsetExists(4));
        $this->assertFalse($immutable->offsetExists(5));
    }

    /**
     * Test offsetGet
     */
    public function testOffsetGet()
    {
        $immutable = $this->object->update(array(1, 2.0, 42, 'x'));

        $this->assertEquals(1, $immutable->offsetGet(0));
        $this->assertEquals(2.0, $immutable->offsetGet(1));
        $this->assertEquals(42, $immutable->offsetGet(2));
        $this->assertEquals('x', $immutable->offsetGet(3));
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
        $immutable = $this->object->update(array('a', 'b', 'c'));

        $this->assertInstanceOf(get_class($immutable), $immutable->offsetUnset(0));
        $this->assertInstanceOf(get_class($immutable), $immutable->offsetUnset(2));

        $this->assertNull($immutable->get(0));
        $this->assertNull($immutable->get(2));
        $this->assertEquals('b', $immutable->get(1));
    }

    /**
     * Test to seek the pointer to given offset
     */
    public function testSeek()
    {
        $immutable = $this->object->update(array(1, 2, 3, 4, 5, 'x'));
        $this->assertEquals(3, $immutable->seek(2));
        $this->assertEquals(2, $immutable->prev());
        $this->assertEquals(4, $immutable->seek(3));
        $this->assertEquals(5, $immutable->next());
        $this->assertEquals('x', $immutable->seek(5));
    }

    /**
     * Test seek to given key
     */
    public function testSeekToKey()
    {
        $immutable = $this->object->update(array('a', 'b', 'c', 'd'));

        $this->assertEquals('a', $immutable->seekToKey(0));
        $this->assertEquals('b', $immutable->seekToKey(1));
        $this->assertEquals('b', $immutable->seekToKey('1', false));
        $this->assertEquals('a', $immutable->prev());

        $this->assertEquals('d', $immutable->seekToKey(3));
        $this->assertEquals('d', $immutable->seekToKey('3', false));
        $this->assertEquals('c', $immutable->prev());

        //!!!! this is the reason to use strict ===
        $this->assertEquals('a', $immutable->seekToKey('blafasel', false));
    }

    /**
     * Test to throw an exception if seek is bigger then offset
     *
     * @expectedException  \OutOfBoundsException
     * @expectedExceptionMessage Invalid seek position: 6
     */
    public function testOutOfBoundsSeekException()
    {
        $immutable = $this->object->update(array(1, 2, 3, 4, 5, 'x'));
        $immutable->seek(6);
    }

    /**
     * Test to throw an exception if seek is bigger then offset
     *
     * @expectedException  \OutOfBoundsException
     *
     * @dataProvider outOfBoundsProvider
     *
     * @param array $aValues
     * @param mixed $mSeekKey
     * @param bool  $bStrict
     */
    public function testOutOfBoundsSeekToKeyException(array $aValues, $mSeekKey, $bStrict)
    {
        $immutable = $this->object->update($aValues);
        $immutable->seekToKey($mSeekKey, $bStrict);
    }

    /**
     * Test the isEmpty function
     */
    public function testIsEmpty()
    {
        $this->assertTrue($this->object->isEmpty());
        $immutable = $this->object->update(array(42));
        $this->assertFalse($immutable->isEmpty());

        $immutable->offsetUnset(0);
        $this->assertTrue($immutable->isEmpty());
    }

    /**
     * Test to get all values as array from collection
     */
    public function testGetAll()
    {
        $this->assertEquals(array(), $this->object->getAll());
        $aArray = array(1, 2, 3);
        $immutable = $this->object->update($aArray);
        $this->assertEquals($aArray, $immutable->getAll());

        $immutable->offsetSet(3, 4);
        $this->assertEquals(array(1, 2, 3, 4), $immutable->getAll());
    }

    /**
     * Test to clear the collection
     */
    public function testClear()
    {
        $immutable = $this->object->update(array(1, 2, 3));
        $this->assertCount(3, $immutable);
        $this->assertEquals(2, $immutable->seek(1));

        $this->assertInstanceOf(get_class($immutable), $immutable->clear());

        $this->assertCount(0, $immutable);
        $immutable->offsetSet(0, 42);
        $this->assertEquals(42, $immutable->current());
    }

    /**
     * Test to get keys from collections
     */
    public function testGetKeys()
    {
        $immutable = $this->object->update(array('x' => 2.5, '3' => 'bla', 0 => 2));
        $this->assertEquals(
            array('x', '3', 0),
            $immutable->getKeys()
        );
    }

    /**
     * @param array    $aValues
     * @param \Closure $cClosure
     * @param array    $aExpectedResult
     *
     * @dataProvider filterProvider
     */
    public function testFilter(array $aValues, \Closure $cClosure, array $aExpectedResult)
    {
        $immutable = $this->object->update($aValues);
        $oResult = $immutable->filter($cClosure);
        $this->assertCount(count($aValues), $immutable);
        $this->assertInstanceOf(get_class($immutable), $oResult);

        foreach ($aExpectedResult as $sKey => $mValue) {
            $this->assertTrue($oResult->offsetExists($sKey));
            $this->assertEquals($mValue, $oResult->offsetGet($sKey));
        }
        $this->assertCount(count($aExpectedResult), $oResult);
    }

    /**
     * Test to use an function for allElements
     */
    public function testForAll()
    {
        $immutable = $this->object->update(array(1, 2, 3, 4, 5, 6));
        $cDecreaseAllValues    = function ($iValue) {
            return $iValue - 1;
        };
        $cIncreaseModTwoValues = function ($iValue, $iKey) {
            return (0 === $iKey % 2) ? $iValue + 1 : $iValue;
        };

        $immutable = $immutable->forAll($cDecreaseAllValues);
        $this->assertEquals(array(0, 1, 2, 3, 4, 5), $immutable->getAll());

        $immutable = $immutable->forAll($cIncreaseModTwoValues);
        $this->assertEquals(array(1, 1, 3, 3, 5, 5), $immutable->getAll());
    }

    /**
     * DataProvider for testFilter
     *
     * @return array
     */
    public function filterProvider()
    {
        $aTestCases = array();
        $cModuloTwo = function ($value) {
            return ($value % 2 === 0);
        };

        $cKeyIsString = function ($value, $key) {
            return (is_string($key));
        };

        $aTestCases['oneElementNullFiltered'] = array(
            'aValues'         => array(1),
            'cClosure'        => $cModuloTwo,
            'aExpectedResult' => array()
        );

        $aTestCases['oneElementOneFiltered'] = array(
            'aValues'         => array(2),
            'cClosure'        => $cModuloTwo,
            'aExpectedResult' => array(2)
        );

        $aTestCases['oneIsString'] = array(
            'aValues'         => array(1, 2, 'x' => 3),
            'cClosure'        => $cKeyIsString,
            'aExpectedResult' => array('x' => 3)
        );

        $aTestCases['allAreStrings'] = array(
            'aValues'         => array('y' => 1, 'a' => 2, 'x' => 3),
            'cClosure'        => $cKeyIsString,
            'aExpectedResult' => array('y' => 1, 'a' => 2, 'x' => 3)
        );

        return $aTestCases;
    }

    /**
     * Test to slice values and create a new collection with these
     *
     * @param array $aValues
     * @param mixed $mKey
     * @param bool  $bStrict
     * @param int   $iLength
     * @param array $aExpectedResult
     *
     * @dataProvider sliceByKeyProvider
     */
    public function testSliceByKey(array $aValues, $mKey, $bStrict, $iLength, array $aExpectedResult)
    {
        $immutable = $this->object->update($aValues);
        $oBackup            = clone $immutable;
        $oResult = $immutable->sliceByKey($mKey, $bStrict, $iLength);

        $this->assertEquals($oBackup, $immutable);
        $this->assertInstanceOf(get_class($immutable), $oResult);
        $this->assertEquals($aExpectedResult, $oResult->getAll());
    }


    /**
     * Test json serialize
     */
    public function testJsonSerialize()
    {
        $empty = $this->object->jsonSerialize();
        $this->assertTrue(is_array($empty));
        $this->assertEmpty($empty);

        $this->object->set(['a' => 1,'b' => 2, 42 => 3]);
        $json = $this->object->jsonSerialize();
        $this->assertTrue(is_array($json));
        $this->assertArrayHasKey('a', $json);
        $this->assertArrayHasKey('b', $json);
        $this->assertArrayHasKey(42, $json);
        $this->assertArrayNotHasKey(1337, $json);
    }

    /**
     * Dataprovider for testSliceByKey
     *
     * @return array
     */
    public function sliceByKeyProvider()
    {
        $aTestCases = array();

        $aTestCases['emptyCollection'] = array(
            'aValues'         => array(),
            'mKey'            => 0,
            'bStrict'         => false,
            'iLength'         => 1,
            'aExpectedResult' => array()
        );

        $aTestCases['simpleSlice'] = array(
            'aValues'         => array(0 => 1),
            'mKey'            => 0,
            'bStrict'         => false,
            'iLength'         => 1,
            'aExpectedResult' => array(0 => 1)
        );

        $aTestCases['keyNotExists'] = array(
            'aValues'         => array(0 => 1),
            'mKey'            => 3,
            'bStrict'         => false,
            'iLength'         => 1,
            'aExpectedResult' => array()
        );

        $aTestCases['keyIsNull'] = array(
            'aValues'         => array(1, 2, 3),
            'mKey'            => null,
            'bStrict'         => false,
            'iLength'         => 1,
            'aExpectedResult' => array(1)
        );

        $aTestCases['keyIsNull2'] = array(
            'aValues'         => array(1, 2, 3),
            'mKey'            => null,
            'bStrict'         => false,
            'iLength'         => 2,
            'aExpectedResult' => array(1, 2)
        );

        $aTestCases['findWithStrict'] = array(
            'aValues'         => array(1, 2, 3),
            'mKey'            => 1,
            'bStrict'         => true,
            'iLength'         => 2,
            'aExpectedResult' => array(1 => 2, 2 => 3)
        );

        $aTestCases['notFindWithStrict'] = array(
            'aValues'         => array(1, 2, 3),
            'mKey'            => '1',
            'bStrict'         => true,
            'iLength'         => 2,
            'aExpectedResult' => array()
        );

        $aTestCases['findWithoutStrict'] = array(
            'aValues'         => array(1, 2, 3),
            'mKey'            => '1',
            'bStrict'         => false,
            'iLength'         => 2,
            'aExpectedResult' => array(1 => 2, 2 => 3)
        );

        $aTestCases['sliceExample'] = array(
            'aValues'         => array('a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5),
            'mKey'            => 'b',
            'bStrict'         => false,
            'iLength'         => 2,
            'aExpectedResult' => array('b' => 2, 'c' => 3)
        );

        $aTestCases['lengthOfNull'] = array(
            'aValues'         => array('a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5),
            'mKey'            => 'b',
            'bStrict'         => false,
            'iLength'         => 0,
            'aExpectedResult' => array()
        );

        $aTestCases['lengthOfOne'] = array(
            'aValues'         => array('a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5),
            'mKey'            => 'b',
            'bStrict'         => false,
            'iLength'         => 1,
            'aExpectedResult' => array('b' => 2)
        );

        $aTestCases['negativeLength'] = array(
            'aValues'         => array('a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5),
            'mKey'            => 'b',
            'bStrict'         => false,
            'iLength'         => -1,
            'aExpectedResult' => array()
        );

        $aTestCases['maxLength'] = array(
            'aValues'         => array('a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5),
            'mKey'            => 'c',
            'bStrict'         => false,
            'iLength'         => PHP_INT_MAX,
            'aExpectedResult' => array('c' => 3, 'd' => 4, 'e' => 5)
        );

        return $aTestCases;
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
     * Provider for testOutOfBoundsSeekToKeyException
     *
     * @return array
     */
    public function outOfBoundsProvider()
    {
        $aTestCases              = array();
        $aTestCases['notStrict'] = array(
            'aValues'  => array(1, 2, 3, 4),
            'mSeekKey' => 4,
            'bStrict'  => false
        );
        $aTestCases['strict']    = array(
            'aValues'  => array(1, 2, 3, 4),
            'mSeekKey' => '1',
            'bStrict'  => true
        );

        return $aTestCases;
    }

}