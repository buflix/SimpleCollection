<?php

namespace Tests\SimpleCollection;

use InvalidArgumentException;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use SimpleCollection\AbstractCollection;
use SimpleCollection\AssocCollection;

/**
 * AssocCollection Test
 *
 * @copyright Felix Buchheim
 * @author    Felix Buchheim <hanibal4nothing@gmail.com>
 */
class AssocCollectionTest extends TestCase
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
    public function setUp(): void
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

        $this->object->set([1, 2, 3]);

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

        $this->object->set([1, false, true, 0, null, '']);

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

        $this->object->set([1, 2, false, null]);

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

        $this->object->set([1, 2, 3, 42, 'x']);

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
        $this->object->set([1, 2.0, 42, 'x']);

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
     * @dataProvider offsetSetExceptionProvider
     *
     * @param $mOffset
     */
    public function testOffsetSetException($mOffset)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->object->offsetSet($mOffset, 42);
    }

    /**
     * Test to unset value by offset
     */
    public function testOffsetUnset()
    {
        $this->assertCount(0, $this->object);
        $this->object->set(['a', 'b', 'c']);

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
        $this->object->set([1, 2, 3, 4, 5, 'x']);
        $this->assertEquals(3, $this->object->seek(2));
        $this->assertEquals(2, $this->object->prev());
        $this->assertEquals(4, $this->object->seek(3));
        $this->assertEquals(5, $this->object->next());
        $this->assertEquals('x', $this->object->seek(5));
    }

    /**
     * Test seek to given key
     */
    public function testSeekToKey()
    {
        $this->object->set(['a', 'b', 'c', 'd']);

        $this->assertEquals('a', $this->object->seekToKey(0));
        $this->assertEquals('b', $this->object->seekToKey(1));
        $this->assertEquals('b', $this->object->seekToKey('1', false));
        $this->assertEquals('a', $this->object->prev());

        $this->assertEquals('d', $this->object->seekToKey(3));
        $this->assertEquals('d', $this->object->seekToKey('3', false));
        $this->assertEquals('c', $this->object->prev());
    }

    /**
     * Test to throw an exception if seek is bigger then offset
     */
    public function testOutOfBoundsSeekException()
    {
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Invalid seek position: 6');
        $this->object->set([1, 2, 3, 4, 5, 'x']);
        $this->object->seek(6);
    }

    /**
     * Test to throw an exception if seek is bigger then offset
     *
     * @dataProvider outOfBoundsProvider
     *
     * @param array $aValues
     * @param mixed $mSeekKey
     * @param bool $bStrict
     */
    public function testOutOfBoundsSeekToKeyException(array $aValues, mixed $mSeekKey, bool $bStrict)
    {
        $this->expectException(OutOfBoundsException::class);
        $this->object->set($aValues);
        $this->object->seekToKey($mSeekKey, $bStrict);
    }

    /**
     * Test the isEmpty function
     */
    public function testIsEmpty()
    {
        $this->assertTrue($this->object->isEmpty());
        $this->object->set([42]);
        $this->assertFalse($this->object->isEmpty());

        $this->object->offsetUnset(0);
        $this->assertTrue($this->object->isEmpty());
    }

    /**
     * Test to get all values as array from collection
     */
    public function testGetAll()
    {
        $this->assertEquals([], $this->object->getAll());
        $aArray = [1, 2, 3];
        $this->object->set($aArray);
        $this->assertEquals($aArray, $this->object->getAll());

        $this->object->offsetSet(3, 4);
        $this->assertEquals([1, 2, 3, 4], $this->object->getAll());
    }

    /**
     * Test to clear the collection
     */
    public function testClear()
    {
        $this->object->set([1, 2, 3]);
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
        $this->object->set(['x' => 2.5, '3' => 'bla', 0 => 2]);
        $this->assertEquals(
            ['x', '3', 0],
            $this->object->getKeys()
        );
    }

    /**
     * @param array $aValues
     * @param callable $cClosure
     * @param array $aExpectedResult
     *
     * @dataProvider filterProvider
     */
    public function testFilter(array $aValues, callable $cClosure, array $aExpectedResult)
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
     * Test to use a function for allElements
     */
    public function testForAll()
    {
        $this->object->set([1, 2, 3, 4, 5, 6]);
        $cDecreaseAllValues = function ($iValue) {
            return $iValue - 1;
        };
        $cIncreaseModTwoValues = function ($iValue, $iKey) {
            return (0 === $iKey % 2) ? $iValue + 1 : $iValue;
        };

        $this->object->forAll($cDecreaseAllValues);
        $this->assertEquals([0, 1, 2, 3, 4, 5], $this->object->getAll());

        $this->object->forAll($cIncreaseModTwoValues);
        $this->assertEquals([1, 1, 3, 3, 5, 5], $this->object->getAll());
    }

    /**
     * DataProvider for testFilter
     *
     * @return array
     */
    public function filterProvider(): array
    {
        $aTestCases = [];
        $cModuloTwo = function ($value) {
            return ($value % 2 === 0);
        };

        $cKeyIsString = function ($value, $key) {
            return (is_string($key));
        };

        $aTestCases['oneElementNullFiltered'] = [
            'aValues' => [1],
            'cClosure' => $cModuloTwo,
            'aExpectedResult' => []
        ];

        $aTestCases['oneElementOneFiltered'] = [
            'aValues' => [2],
            'cClosure' => $cModuloTwo,
            'aExpectedResult' => [2]
        ];

        $aTestCases['oneIsString'] = [
            'aValues' => [1, 2, 'x' => 3],
            'cClosure' => $cKeyIsString,
            'aExpectedResult' => ['x' => 3]
        ];

        $aTestCases['allAreStrings'] = [
            'aValues' => ['y' => 1, 'a' => 2, 'x' => 3],
            'cClosure' => $cKeyIsString,
            'aExpectedResult' => ['y' => 1, 'a' => 2, 'x' => 3]
        ];

        return $aTestCases;
    }

    /**
     * Test to slice values and create a new collection with these
     *
     * @param array $aValues
     * @param string|int|null $mKey
     * @param bool $bStrict
     * @param int $iLength
     * @param array $aExpectedResult
     *
     * @dataProvider sliceByKeyProvider
     */
    public function testSliceByKey(array $aValues, string|int|null $mKey, bool $bStrict, int $iLength, array $aExpectedResult)
    {
        $this->object->set($aValues);
        $oBackup = clone $this->object;
        $oResult = $this->object->sliceByKey($mKey, $bStrict, $iLength);

        $this->assertEquals($oBackup, $this->object);
        $this->assertInstanceOf(get_class($this->object), $oResult);
        $this->assertEquals($aExpectedResult, $oResult->getAll());
    }

    /**
     * Test contain func on value
     */
    public function testContainIsEven()
    {
        $this->object->set([1, 3, 4, 5]);
        $this->assertTrue($this->object->contains(function ($value) {
            return ($value % 2) === 0;
        }));
    }

    /**
     * Test contain func false on value
     */
    public function testContainStringContains()
    {
        $this->object->set(['banana', 'wow', 'lol']);
        $this->assertFalse($this->object->contains(function ($value) {
            return ($value === 'cs:go');
        }));
    }

    /**
     * Test contain func on key
     */
    public function testContainsKey()
    {
        $this->object->set([1 => 1, 3 => 3, 6 => 4, 8 => 5]);
        $this->assertTrue($this->object->contains(function ($value, $key) {
            $this->assertTrue($key < 9);
            return ($key % 6) === 0;
        }));
    }

    /**
     * Test contain func on key
     */
    public function testNotContainsKey()
    {
        $this->object->set([0 => 1, 3 => 3, 6 => 4, 8 => 5]);

        $this->assertFalse($this->object->contains(function ($value, $key) {
            return ($key === 2);
        }));
    }


    /**
     * Test json serialize
     */
    public function testJsonSerialize()
    {
        $empty = $this->object->jsonSerialize();
        $this->assertTrue(is_array($empty));
        $this->assertEmpty($empty);

        $this->object->set(['a' => 1, 'b' => 2, 42 => 3]);
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
    public function sliceByKeyProvider(): array
    {
        $aTestCases = [];

        $aTestCases['emptyCollection'] = [
            'aValues' => [],
            'mKey' => 0,
            'bStrict' => false,
            'iLength' => 1,
            'aExpectedResult' => []
        ];

        $aTestCases['simpleSlice'] = [
            'aValues' => [0 => 1],
            'mKey' => 0,
            'bStrict' => false,
            'iLength' => 1,
            'aExpectedResult' => [0 => 1]
        ];

        $aTestCases['keyNotExists'] = [
            'aValues' => [0 => 1],
            'mKey' => 3,
            'bStrict' => false,
            'iLength' => 1,
            'aExpectedResult' => []
        ];

        $aTestCases['keyIsNull'] = [
            'aValues' => [1, 2, 3],
            'mKey' => null,
            'bStrict' => false,
            'iLength' => 1,
            'aExpectedResult' => [1]
        ];

        $aTestCases['keyIsNull2'] = [
            'aValues' => [1, 2, 3],
            'mKey' => null,
            'bStrict' => false,
            'iLength' => 2,
            'aExpectedResult' => [1, 2]
        ];

        $aTestCases['findWithStrict'] = [
            'aValues' => [1, 2, 3],
            'mKey' => 1,
            'bStrict' => true,
            'iLength' => 2,
            'aExpectedResult' => [1 => 2, 2 => 3]
        ];

        $aTestCases['notFindWithStrict'] = [
            'aValues' => [1, 2, 3],
            'mKey' => '1',
            'bStrict' => true,
            'iLength' => 2,
            'aExpectedResult' => []
        ];

        $aTestCases['findWithoutStrict'] = [
            'aValues' => [1, 2, 3],
            'mKey' => '1',
            'bStrict' => false,
            'iLength' => 2,
            'aExpectedResult' => [1 => 2, 2 => 3]
        ];

        $aTestCases['sliceExample'] = [
            'aValues' => ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5],
            'mKey' => 'b',
            'bStrict' => false,
            'iLength' => 2,
            'aExpectedResult' => ['b' => 2, 'c' => 3]
        ];

        $aTestCases['lengthOfNull'] = [
            'aValues' => ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5],
            'mKey' => 'b',
            'bStrict' => false,
            'iLength' => 0,
            'aExpectedResult' => []
        ];

        $aTestCases['lengthOfOne'] = [
            'aValues' => ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5],
            'mKey' => 'b',
            'bStrict' => false,
            'iLength' => 1,
            'aExpectedResult' => ['b' => 2]
        ];

        $aTestCases['negativeLength'] = [
            'aValues' => ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5],
            'mKey' => 'b',
            'bStrict' => false,
            'iLength' => -1,
            'aExpectedResult' => []
        ];

        $aTestCases['maxLength'] = [
            'aValues' => ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5],
            'mKey' => 'c',
            'bStrict' => false,
            'iLength' => PHP_INT_MAX,
            'aExpectedResult' => ['c' => 3, 'd' => 4, 'e' => 5]
        ];

        return $aTestCases;
    }

    /**
     * DataProvider for testOffsetSetException
     *
     * @return array
     */
    public function offsetSetExceptionProvider(): array
    {
        $aTestCases = [];

        $aTestCases['null'] = ['mOffset' => null];
        $aTestCases['float'] = ['mOffset' => 2.5];
        $aTestCases['bool'] = ['mOffset' => false];
        $aTestCases['object'] = ['mOffset' => new \stdClass()];

        return $aTestCases;
    }

    /**
     * Provider for testOutOfBoundsSeekToKeyException
     *
     * @return array
     */
    public function outOfBoundsProvider(): array
    {
        $aTestCases = [];
        $aTestCases['notStrict'] = [
            'aValues' => [1, 2, 3, 4],
            'mSeekKey' => 4,
            'bStrict' => false
        ];
        $aTestCases['strict'] = [
            'aValues' => [1, 2, 3, 4],
            'mSeekKey' => '1',
            'bStrict' => true
        ];

        return $aTestCases;
    }
}
