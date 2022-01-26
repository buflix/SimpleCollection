<?php

namespace Tests\SimpleCollection;

use SimpleCollection\ArrayCollection;

/**
 * ArrayCollection Test
 *
 * @copyright Felix Buchheim
 * @author    Felix Buchheim <hanibal4nothing@gmail.com>
 */
class ArrayCollectionTest extends AssocCollectionTest
{

    /**
     * Object to test
     *
     * @var ArrayCollection
     */
    protected $object;

    /**
     * Set the object to test
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->object = new ArrayCollection();
    }

    /**
     * Test add function
     */
    public function testAdd()
    {
        $this->assertCount(0, $this->object);
        $this->object->add(2);
        $this->assertNotEmpty($this->object);
        $this->object->add(42);

        $this->assertTrue($this->object->offsetExists(0));
        $this->assertTrue($this->object->offsetExists(1));
    }

    /**
     * Test the reset on set()
     */
    public function testResetValuesOnSet()
    {
        $this->object->set([2 => 42, '3' => 'mc', '4' => 'ab']);

        $this->assertEquals(42, $this->object->offsetGet(0));
        $this->assertEquals('mc', $this->object->offsetGet(1));
        $this->assertEquals('ab', $this->object->offsetGet(2));
        $this->assertFalse($this->object->offsetExists(3));
    }

    /**
     * Test to get keys from collections
     */
    public function testGetKeys()
    {
        $this->object->set(['x' => 2.5, '3' => 'bla', 0 => 2]);
        $this->assertEquals(
            [0, 1, 2],
            $this->object->getKeys()
        );
    }

    /**
     * TestConstructor
     *
     * @param array $aValues
     * @param int $iAssertCount
     *
     * @dataProvider constructProvider
     */
    public function testConstruct(array $aValues, $iAssertCount)
    {
        $this->object->set($aValues);
        $this->assertCount($iAssertCount, $this->object);
    }

    /**
     * Check to reset keys on construct
     *
     * @param array $aValues
     * @param int $iValueCount
     *
     * @dataProvider constructProvider
     */
    public function testResetKeys(array $aValues, $iValueCount)
    {
        $this->object->set($aValues);
        for ($i = 0; $i < $iValueCount; $i++) {
            $this->assertTrue($this->object->offsetExists($i));
        }
        $this->assertFalse($this->object->offsetExists($iValueCount + 1));
    }

    /**
     * Test contain func on key
     */
    public function testNotContainsKey()
    {
        $this->object->set([0 => 1, 3 => 3, 6 => 4, 8 => 5]);

        $this->assertTrue($this->object->contains(function ($value, $key) {
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

        $this->object->set([1, 2, 3]);
        $json = $this->object->jsonSerialize();
        $this->assertTrue(is_array($json));
        $this->assertArrayHasKey(0, $json);
        $this->assertArrayHasKey(1, $json);
        $this->assertArrayHasKey(2, $json);
        $this->assertArrayNotHasKey(3, $json);
    }

    /**
     * DataProvider for AssocCollectionTest::testFilter
     *
     * @return array
     */
    public function filterProvider(): array
    {
        $aTestCases = [];
        $cModuloTwo = function ($value) {
            return ($value % 2 === 0);
        };

        $cKeyIsBiggerThenOne = function ($value, $key) {
            return ($key > 1);
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

        $aTestCases['oneIsBiggerThenOne'] = [
            'aValues' => [1, 2, 'x' => 3],
            'cClosure' => $cKeyIsBiggerThenOne,
            'aExpectedResult' => [3]
        ];

        return $aTestCases;
    }

    /**
     * DataProvider for testConstruct
     *
     * @return array
     */
    public function constructProvider()
    {
        $aTestCases = [];
        $aTestCases['emptyArray'] = [
            'aValues' => [],
            'iAssertCount' => 0
        ];
        $aTestCases['oneValue'] = [
            'aValues' => [42],
            'iAssertCount' => 1
        ];
        $aTestCases['multipleValues'] = [
            'aValues' => [42, 'x', 'abc'],
            'iAssertCount' => 3
        ];
        $aTestCases['indexValues'] = [
            'aValues' => ['a' => 42, 2 => 'x', '3' => 'abc'],
            'iAssertCount' => 3
        ];

        return $aTestCases;
    }

    /**
     * Dataprovider for AssocCollectionTest::testSliceByKey
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
            'aExpectedResult' => [2, 3]
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
            'aExpectedResult' => [2, 3]
        ];

        $aTestCases['sliceExample'] = [
            'aValues' => ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5],
            'mKey' => 1,
            'bStrict' => true,
            'iLength' => 2,
            'aExpectedResult' => [2, 3]
        ];

        $aTestCases['lengthOfNull'] = [
            'aValues' => ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5],
            'mKey' => 1,
            'bStrict' => false,
            'iLength' => 0,
            'aExpectedResult' => []
        ];

        $aTestCases['lengthOfOne'] = [
            'aValues' => ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5],
            'mKey' => 1,
            'bStrict' => true,
            'iLength' => 1,
            'aExpectedResult' => [2]
        ];

        $aTestCases['negativeLength'] = [
            'aValues' => ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5],
            'mKey' => 1,
            'bStrict' => false,
            'iLength' => -1,
            'aExpectedResult' => []
        ];

        $aTestCases['maxLength'] = [
            'aValues' => ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5],
            'mKey' => 2,
            'bStrict' => true,
            'iLength' => PHP_INT_MAX,
            'aExpectedResult' => [3, 4, 5]
        ];

        return $aTestCases;
    }

}
