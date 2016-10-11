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
        $this->object->set(array(2 => 42, '3' => 'mc', '4' => 'ab'));

        $this->assertEquals(42, $this->object->offsetGet(0));
        $this->assertEquals('mc', $this->object->offsetGet(1));
        $this->assertEquals('ab', $this->object->offsetGet(2));
        $this->assertFalse($this->object->offsetExists(3));
    }
}