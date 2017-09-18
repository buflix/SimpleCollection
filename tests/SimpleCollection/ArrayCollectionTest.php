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
    public function setUp()
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
        $immutable = $this->object->update(array(2 => 42, '3' => 'mc', '4' => 'ab'));

        $this->assertEquals(42, $immutable->offsetGet(0));
        $this->assertEquals('mc', $immutable->offsetGet(1));
        $this->assertEquals('ab', $immutable->offsetGet(2));
        $this->assertFalse($immutable->offsetExists(3));
    }

    /**
     * Test to get keys from collections
     */
    public function testGetKeys()
    {
        $immutable = $this->object->update(array('x' => 2.5, '3' => 'bla', 0 => 2));
        $this->assertEquals(
            array(0, 1, 2),
            $immutable->getKeys()
        );
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
        $immutable = $this->object->update($aValues);
        $this->assertCount($iAssertCount, $immutable);
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
        $immutable = $this->object->update($aValues);
        for ($i = 0; $i < $iValueCount; $i++) {
            $this->assertTrue($immutable->offsetExists($i));
        }
        $this->assertFalse($immutable->offsetExists($iValueCount + 1));
    }


    /**
     * Test json serialize
     */
    public function testJsonSerialize()
    {
        $empty = $this->object->jsonSerialize();
        $this->assertTrue(is_array($empty));
        $this->assertEmpty($empty);

        $this->object->set([1,2,3]);
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
    public function filterProvider()
    {
        $aTestCases = array();
        $cModuloTwo = function ($value) {
            return ($value % 2 === 0);
        };

        $cKeyIsBiggerThenOne = function ($value, $key) {
            return ($key > 1);
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

        $aTestCases['oneIsBiggerThenOne'] = array(
            'aValues'         => array(1, 2, 'x' => 3),
            'cClosure'        => $cKeyIsBiggerThenOne,
            'aExpectedResult' => array(2 => 3)
        );

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
    /**
     * Dataprovider for AssocCollectionTest::testSliceByKey
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
            'aExpectedResult' => array(2, 3)
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
            'aExpectedResult' => array(2, 3)
        );

        $aTestCases['sliceExample'] = array(
            'aValues'         => array('a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5),
            'mKey'            => 1,
            'bStrict'         => true,
            'iLength'         => 2,
            'aExpectedResult' => array(2, 3)
        );

        $aTestCases['lengthOfNull'] = array(
            'aValues'         => array('a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5),
            'mKey'            => 1,
            'bStrict'         => false,
            'iLength'         => 0,
            'aExpectedResult' => array()
        );

        $aTestCases['lengthOfOne'] = array(
            'aValues'         => array('a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5),
            'mKey'            => 1,
            'bStrict'         => true,
            'iLength'         => 1,
            'aExpectedResult' => array(2)
        );

        $aTestCases['negativeLength'] = array(
            'aValues'         => array('a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5),
            'mKey'            => 1,
            'bStrict'         => false,
            'iLength'         => -1,
            'aExpectedResult' => array()
        );

        $aTestCases['maxLength'] = array(
            'aValues'         => array('a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5),
            'mKey'            => 2,
            'bStrict'         => true,
            'iLength'         => PHP_INT_MAX,
            'aExpectedResult' => array(3, 4, 5)
        );

        return $aTestCases;
    }

}