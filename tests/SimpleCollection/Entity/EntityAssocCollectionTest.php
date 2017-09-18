<?php

namespace Tests\SimpleCollection\Entity;

use PHPUnit\Framework\TestCase;
use SimpleCollection\Entity\EntityAssocCollection;

/**
 * EntityAssocCollectionTest
 *
 * @package Tests\SimpleCollection\Entity
 * @author  Felix Buchheim <hanibal4nothing@gmail.com>
 */
class EntityAssocCollectionTest extends TestCase
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
        $this->assertCount(0, $this->object);

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
        $this->assertCount(0, $this->object);

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
        $this->assertArrayHasKey('X', $json);
        $this->assertArrayHasKey(2, $json);
        $this->assertArrayHasKey('blafasel', $json);
        $this->assertArrayNotHasKey(1337, $json);
    }

    /**
     * Test to add an entity to collection
     */
    public function testAdd()
    {
        $this->assertCount(0, $this->object);

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
        $oFirstEntity  = new DummyEntity('42');
        $oSecondEntity = new DummyEntity('xxXXxx');
        $this->object->add($oFirstEntity)
            ->add($oSecondEntity);

        $this->assertEquals(
            array(
                '42'     => array('index' => '42', 'child' => null),
                'xxXXxx' => array('index' => 'xxXXxx', 'child' => null)
            ),
            $this->object->toArray()
        );

        $this->object->get('42')->setChild(new DummyEntity(44));

        $this->assertEquals(
            array(
                '42'     => array('index' => '42', 'child' => array('index' => 44, 'child' => null)),
                'xxXXxx' => array('index' => 'xxXXxx', 'child' => null)
            ),
            $this->object->toArray()
        );
    }

    /**
     * Test check class exception
     *
     * @expectedException \InvalidArgumentException
     */
    public function testCheckClassException()
    {
        $oMock = $this->createMock('SimpleCollection\Entity\EntityInterface');
        $this->object->add($oMock);
    }
}