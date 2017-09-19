<?php

namespace Tests\SimpleCollection\Base;

use PHPUnit\Framework\TestCase;
use SimpleCollection\Base\ArraySeekableCollection;

/**
 * Test of ArraySeekableCollection
 *
 * @copyright Felix Buchheim
 * @author    Felix Buchheim <hanibal4nothing@gmail.com>
 */
class ArraySeekableCollectionTest extends TestCase
{

    /**
     * @var ArraySeekableCollection
     */
    protected $object;

    /**
     * Setup the testCase
     */
    public function setUp()
    {
        parent::setUp();
        $this->object = new ArraySeekableCollection();
    }
    /**
     * TestCase for empty Constructor
     */
    public function testEmptyConstructor()
    {
        $this->assertCount(0, $this->object);
    }

    /**
     * Test count and values set in construct
     */
    public function testCount()
    {
        $collection = new ArraySeekableCollection();
        $this->assertEquals(0, $collection->count());

        $collection = new ArraySeekableCollection([1,2,3, 'lee' => 7]);
        $this->assertEquals(4, $collection->count());
        $this->assertCount(4, $collection);
    }

    /**
     * Test to seek the pointer to given offset
     */
    public function testSeek()
    {
        $collection = new ArraySeekableCollection(array(1, 2, 3, 4, 5, 'x'));
        $this->assertEquals(3, $collection->seek(2));
        $this->assertEquals(2, $collection->prev());
        $this->assertEquals(4, $collection->seek(3));
        $this->assertEquals(5, $collection->next());
        $this->assertEquals('x', $collection->seek(5));
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
     * Test to set value by offset
     */
    public function testOffsetSet()
    {
        $this->assertInstanceOf(get_class($this->object), $this->object->offsetSet(12, 42));
        $this->assertEquals(42, $this->object->offsetGet(12));
        $this->assertInstanceOf(get_class($this->object), $this->object->offsetSet(12, 'x'));
        $this->assertEquals('x', $this->object->offsetGet(12));

        $this->assertInstanceOf(get_class($this->object), $this->object->offsetSet('', 'y'));
        $this->assertEquals('y', $this->object->offsetGet(''));
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

        $this->assertNull(@$this->object->offsetGet(0));
        $this->assertNull(@$this->object->offsetGet(2));
        $this->assertEquals('b', $this->object->offsetGet(1));
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
     * Test rewind function to reset the pointer
     */
    public function testRewind()
    {
        $this->assertFalse($this->object->rewind());
        $this->object->set([1,2,3, 'test' => 1]);

        $this->assertSame(1, $this->object->current());
        $this->assertSame(2, $this->object->next());
        $this->assertSame(3, $this->object->next());
        $this->assertSame(1, $this->object->next());

        $this->assertSame(1, $this->object->rewind());
        $this->assertSame(1, $this->object->current());
        $this->assertSame(2, $this->object->next());
    }

    /**
     * Test end function
     */
    public function testEnd()
    {
        $this->assertFalse($this->object->end());
        $this->object->set([1,2,3, 'test' => 4]);

        $this->assertSame(1, $this->object->current());
        $this->assertSame(4, $this->object->end());
        $this->assertSame('test', $this->object->key());

        $this->assertFalse($this->object->next());
        $this->assertFalse($this->object->valid());
        $this->assertSame(4, $this->object->end());
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
     * test array access
     */
    public function testArrayAccess()
    {
        $this->object['blafasel'] = 42;
        $this->object['foo'] = 2810;

        $this->assertEquals(2, $this->object->count());
        $this->assertEquals(42, $this->object['blafasel']);
        $this->assertEquals(42, $this->object['blafasel']);
    }

}