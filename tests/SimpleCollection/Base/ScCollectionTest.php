<?php

namespace Tests\SimpleCollection\Base;

use PHPUnit\Framework\TestCase;
use SimpleCollection\Base\ScCollection;

/**
 * Test of Collection
 *
 * @copyright Felix Buchheim
 * @author    Felix Buchheim <hanibal4nothing@gmail.com>
 */
class ScCollectionTest extends TestCase
{

    /**
     * @var ScCollection
     */
    protected $object;

    /**
     * Setup the testCase
     */
    public function setUp()
    {
        parent::setUp();
        $this->object = new ScCollection();
    }

    /***
     * Test scNext and scPrev
     */
    public function testScNextAndPrev()
    {
        $this->assertEquals(ScCollection::NOT_SET_FLAG, $this->object->scNext());
        $this->assertEquals(ScCollection::NOT_SET_FLAG, $this->object->scPrev());

        $this->object->set(array(1, false, true, 0, null, ''));

        $this->assertEquals(ScCollection::NOT_SET_FLAG, $this->object->scPrev());

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
        $this->assertEquals(ScCollection::NOT_SET_FLAG, $this->object->scNext());
    }
}