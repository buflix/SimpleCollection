<?php

namespace Tests\SimpleCollection\Base;

use PHPUnit\Framework\TestCase;
use SimpleCollection\Base\Collection;

/**
 * Test of Collection
 *
 * @copyright Felix Buchheim
 * @author    Felix Buchheim <hanibal4nothing@gmail.com>
 */
class CollectionTest extends TestCase
{

    /**
     * @var Collection
     */
    protected $object;

    /**
     * Setup the testCase
     */
    public function setUp()
    {
        parent::setUp();
        $this->object = new Collection();
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
     * Test seek to given key
     */
    public function testSeekToKey()
    {
        $this->object->set(array('a', 'b', 'c', 'd'));

        $this->assertEquals('a', $this->object->seekToKey(0));
        $this->assertEquals('b', $this->object->seekToKey(1));
        $this->assertEquals('b', $this->object->seekToKey('1', false));
        $this->assertEquals('a', $this->object->prev());

        $this->assertEquals('d', $this->object->seekToKey(3));
        $this->assertEquals('d', $this->object->seekToKey('3', false));
        $this->assertEquals('c', $this->object->prev());

        //!!!! this is the reason to use strict ===
        $this->assertEquals('a', $this->object->seekToKey('blafasel', false));
    }

    /**
     * Test to throw an exception if seek is bigger then offset
     *
     * @expectedException  \OutOfBoundsException
     *
     * @dataProvider outOfBoundsProvider
     *
     * @param array $values
     * @param mixed $seekKey
     * @param bool  $strict
     */
    public function testOutOfBoundsSeekToKeyException(array $values, $seekKey, $strict)
    {
        $this->object->set($values);
        $this->object->seekToKey($seekKey, $strict);
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

    /**
     * Test empty on collection
     */
    public function testIsEmpty()
    {
        $this->assertTrue($this->object->isEmpty());
        $this->object->set([1,2,3,4]);
        $this->assertFalse($this->object->isEmpty());
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
     * Test resetKeys
     */
    public function testResetKeys()
    {
        $params = $this->object->set([1 => 3, 3 => 7])
            ->resetKeys()
            ->getAll();

        $this->assertArrayHasKey(0, $params);
        $this->assertArrayHasKey(1, $params);
        $this->assertArrayNotHasKey(3, $params);
        $this->assertEquals(7, $this->object->get(1));
    }

    /**
     * Test to clear the collection
     */
    public function testClear()
    {
        $this->object->set(array(1, 2, 3));
        $this->assertCount(3, $this->object);
        $this->assertEquals(2, $this->object->seek(1));

        $this->assertInstanceOf(get_class($this->object), $this->object->clear());

        $this->assertCount(0, $this->object);
        $this->object->offsetSet(0, 42);
        $this->assertEquals(42, $this->object->current());
    }
    /**
     * Test to get keys from collections
     */
    public function testGetKeys()
    {
        $this->object->set(array('x' => 2.5, '3' => 'bla', 0 => 2));
        $this->assertEquals(
            array('x', '3', 0),
            $this->object->getKeys()
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
        $this->object->set($aValues);
        $oResult = $this->object->filter($cClosure);
        $this->assertCount(count($aValues), $this->object);
        $this->assertInstanceOf(get_class($this->object), $oResult);

        foreach ($aExpectedResult as $sKey => $mValue) {
            $this->assertTrue($oResult->offsetExists($sKey));
            $this->assertEquals($mValue, $oResult->offsetGet($sKey));
        }
        $this->assertCount(count($aExpectedResult), $oResult);
    }

    /**
     * Test to use an function for allElements
     */
    public function testUpdateItems()
    {
        $this->object->set(array(1, 2, 3, 4, 5, 6));
        $decreaseAllValues    = function ($key, $value) {
            return $value - 1;
        };
        $increaseModTwoValues = function ($key, $value) {
            return (0 === $key % 2) ? $value + 1 : $value;
        };

        $this->object->updateItems($decreaseAllValues);
        $this->assertEquals(array(0, 1, 2, 3, 4, 5), $this->object->getAll());

        $this->object->updateItems($increaseModTwoValues);
        $this->assertEquals(array(1, 1, 3, 3, 5, 5), $this->object->getAll());
    }

    /**
     * Test to use an function for allElements
     */
    public function testForAll()
    {
        $this->object->set(array(1, 2, 3, 4, 5, 6));
        $sumValues = $sumKeys = 0;
        $summarize = function($key, $value) use (&$sumValues, &$sumKeys) {
            $sumValues += $value;
            $sumKeys += $key;
        };

        $this->object->forAll($summarize);
        $this->assertEquals(21, $sumValues);
        $this->assertEquals(15, $sumKeys);
        $this->assertEquals(array(1, 2, 3, 4, 5, 6), $this->object->getAll());
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
        $this->object->set($aValues);
        $oBackup            = clone $this->object;
        $oResult = $this->object->sliceByKey($mKey, $bStrict, $iLength);

        $this->assertEquals($oBackup, $this->object);
        $this->assertInstanceOf(get_class($this->object), $oResult);
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
     * Test magic get and set
     */
    public function testMagicGetSet()
    {
        $this->object->leet = 1337;
        $this->assertEquals(1, $this->object->count());
        $this->assertEquals(1337, $this->object->leet);
    }

    /**
     * Test magic isset
     */
    public function testMagicIsset()
    {
        $this->assertFalse(isset($this->object->something));
        $this->object->something = 42;

        $this->assertTrue(isset($this->object->something));
    }

    /**
     * Test magic unset
     */
    public function testMagicUnset()
    {
        $this->object->offsetSet(42, 'blafooo');
        $this->assertCount(1, $this->object);

        unset($this->object->{42});
        $this->assertTrue($this->object->isEmpty());
    }

    /**
     * Test to string method
     */
    public function testToString()
    {
        $this->object->set(['a' => 'foo', 'b' => 3, 42 => 'leet']);
        $expected = '{"a":"foo","b":3,"42":"leet"}';

        $this->assertSame($expected, (string) $this->object);
    }

}