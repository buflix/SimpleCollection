<?php

namespace Tests\SimpleCollection\Entity;

use SimpleCollection\Entity\AbstractEntity;
use SimpleCollection\Entity\AssocEntityInterface;

/**
 * DummyEntity
 *
 * @copyright Felix Buchheim
 * @author    Felix Buchheim <hanibal4nothing@gmail.com>
 */
class DummyEntity extends AbstractEntity implements AssocEntityInterface
{

    /**
     * Dummy property
     *
     * @var mixed
     */
    protected $index;

    /**
     * Child entity
     *
     * @var DummyEntity
     */
    protected $child;

    /**
     * DummyEntity constructor.
     *
     * @param $mIndex
     */
    public function __construct($mIndex)
    {
        $this->index = $mIndex;
    }

    /**
     * Return Index
     *
     * @return mixed
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Set Index to this object
     *
     * @param mixed $index
     *
     * @return $this
     */
    public function setIndex($index)
    {
        $this->index = $index;

        return $this;
    }

    /**
     * @return int|string
     */
    public function getCollectionIndex(): int|string
    {
        return $this->getIndex();
    }

    /**
     * Return Child
     *
     * @return DummyEntity
     */
    public function getChild()
    {
        return $this->child;
    }

    /**
     * Set Child to this object
     *
     * @param DummyEntity $child
     *
     * @return $this
     */
    public function setChild(DummyEntity $child)
    {
        $this->child = $child;

        return $this;
    }
}
