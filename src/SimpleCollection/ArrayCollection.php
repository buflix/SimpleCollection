<?php

namespace SimpleCollection;

/**
 * Array collection
 *
 * @package SimpleCollection
 * @author  Felix Buchheim <hanibal4nothing@gmail.com>
 */
class ArrayCollection extends AbstractCollection
{

    /**
     * int indexed array with entities
     *
     * @var EntityInterface[]
     */
    protected $entities = array();

    /**
     * add a entity to the collection
     *
     * @param EntityInterface $oEntity
     *
     * @return $this
     */
    public function add(EntityInterface $oEntity)
    {
        $this->entities[] = $oEntity;

        return $this;
    }

    /**
     * reset the collection keys
     *
     * @return $this
     */
    public function resetKeys()
    {
        $this->entities = array_values($this->entities);

        return $this;
    }
}