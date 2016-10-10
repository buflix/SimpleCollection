<?php

namespace Tests\SimpleCollection\Entity;

use SimpleCollection\Entity\EntityAssocCollection;

/**
 * EntityAssocCollectionTest
 *
 * @package Tests\SimpleCollection\Entity
 * @author  Felix Buchheim <hanibal4nothing@gmail.com>
 */
class EntityAssocCollectionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Object to test
     *
     * @var EntityAssocCollection
     */
    protected $object;

    /**
     * Setup the object to test
     */
    public function setUp()
    {
        parent::setUp();
        $this->object = new EntityAssocCollection();
    }

    /**
     * TestConstruct
     */
    public function testConstruct()
    {
        $this->assertEmpty($this->object);

        $this->object = new EntityAssocCollection(
            array(
                new DummyEntity('abc'),
                new DummyEntity('cde'),
                new DummyEntity('efg'),
            )
        );
        $this->object->offsetExists('abc');
        $this->object->offsetExists('cde');
        $this->object->offsetExists('efg');

        $this->assertCount(3, $this->object);
    }

    /**
     * Check if given entity exists
     */
    public function testEntityExists()
    {
        $this->assertFalse($this->object->entityExists(new DummyEntity('def')));

        $this->object->add(new DummyEntity(42));
        $this->assertTrue($this->object->entityExists(new DummyEntity(42)));
        $this->assertFalse($this->object->entityExists(new DummyEntity('def')));
    }

    /**
     * Test setEntities
     */
    public function testSet()
    {
        $this->assertEmpty($this->object);

        $this->object->set(array(
            new DummyEntity('abc'),
            new DummyEntity('cde'),
            new DummyEntity('efg'),
        ));
        $this->object->offsetExists('abc');
        $this->object->offsetExists('cde');
        $this->object->offsetExists('efg');

        $this->assertCount(3, $this->object);
    }

    /**
     * Test to add an entity to collection
     */
    public function testAdd()
    {
        $this->assertEmpty($this->object);

        $this->object->add(new DummyEntity(12))
            ->add(new DummyEntity('x'));
        $this->assertCount(2, $this->object);
        $this->object->offsetExists(2);
        $this->object->offsetExists('x');
    }

    /**
     * Test offSetSet
     */
    public function testOffsetSet()
    {
        $this->object->offsetSet('42', new DummyEntity('xxx'));
        $this->assertTrue($this->object->offsetExists('42'));
    }

    /**
     * Test to create an array from collection
     */
    public function testToArray()
    {
        $this->object->add(new DummyEntity('42'))
            ->add(new DummyEntity('xxXXxx'));

        $this->assertEquals(
            array(
                '42'     => array('index' => '42'),
                'xxXXxx' => array('index' => 'xxXXxx')
            ),
            $this->object->toArray()
        );
    }
}