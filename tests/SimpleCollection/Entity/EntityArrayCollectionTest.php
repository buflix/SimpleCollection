<?php

namespace Tests\SimpleCollection\Entity;

use SimpleCollection\Entity\EntityArrayCollection;

/**
 * EntityArrayCollectionTest
 *
 * @package Tests\SimpleCollection\Entity
 * @author  Felix Buchheim <hanibal4nothing@gmail.com>
 */
class EntityArrayCollectionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Object to test
     *
     * @var EntityArrayCollection
     */
    protected $object;

    /**
     * Setup the object to test
     */
    public function setUp()
    {
        parent::setUp();
        $this->object = new EntityArrayCollection();
    }

    /**
     * Test construct to reset array keys
     */
    public function testConstruct()
    {
        $this->assertCount(0, $this->object);
        $this->object = new EntityArrayCollection(
            array(
                'x'   => new DummyEntity('bla'),
                42    => new DummyEntity(2),
                '3.4' => new DummyEntity(3.4)
            )
        );

        $this->assertCount(3, $this->object);
        $this->assertTrue($this->object->offsetExists(0));
        $this->assertTrue($this->object->offsetExists(1));
        $this->assertTrue($this->object->offsetExists(2));

        $this->assertFalse($this->object->offsetExists('x'));
        $this->assertFalse($this->object->offsetExists(42));
        $this->assertFalse($this->object->offsetExists('3.4'));
    }

    /**
     * Check to reset keys on set
     */
    public function testSet()
    {
        $aEntities = array(
            'x'   => new DummyEntity('X'),
            'bla' => new DummyEntity(2),
            3     => new DummyEntity('blafasel')
        );

        $immutable = $this->object->set($aEntities);

        $this->assertCount(3, $immutable);
        $this->assertTrue($immutable->offsetExists(0));
        $this->assertTrue($immutable->offsetExists(1));
        $this->assertTrue($immutable->offsetExists(2));

        $this->assertFalse($immutable->offsetExists('x'));
        $this->assertFalse($immutable->offsetExists('bla'));
        $this->assertFalse($immutable->offsetExists(3));
    }
    /**
     * Test json serialize
     */
    public function testJsonSerialize()
    {
        $empty = $this->object->jsonSerialize();
        $this->assertTrue(is_array($empty));
        $this->assertEmpty($empty);

        $aEntities = array(
            'x'   => new DummyEntity('X'),
            'bla' => new DummyEntity(2),
            3     => new DummyEntity('blafasel')
        );
        $this->object->set($aEntities);

        $json = $this->object->jsonSerialize();
        $this->assertArrayHasKey(0, $json);
        $this->assertArrayHasKey(1, $json);
        $this->assertArrayHasKey(2, $json);
        $this->assertArrayNotHasKey(1337, $json);
    }


    /**
     * Test to add new entity to collection
     */
    public function testAdd()
    {
        $this->assertCount(0, $this->object);

        $this->object->add(new DummyEntity(42));
        $this->assertCount(1, $this->object);
        $this->assertTrue($this->object->offsetExists(0));

        $this->object->add(new DummyEntity('x'));
        $this->assertCount(2, $this->object);
        $this->assertTrue($this->object->offsetExists(1));
    }

    /**
     * Test exception if not entityInterface instance given
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Expect entity of class \SimpleCollection\Entity\EntityInterface
     */
    public function testCheckClass()
    {
        $this->object->add(new \stdClass());
    }
}