<?php

namespace SimpleCollection\Entity;

/**
 * Array collection
 *
 * @package SimpleCollection
 * @author  Felix Buchheim <hanibal4nothing@gmail.com>
 */
class EntityListCollection extends AbstractEntityCollection
{

    /**
     * EntityArrayCollection constructor.
     *
     * @param array|EntityInterface[] $entities
     */
    public function __construct($entities = array())
    {
        parent::__construct($entities);
        $this->resetKeys();
    }

    /**
     * Set all values and reset keys
     *
     * @param EntityInterface[] $values
     *
     * @return $this
     */
    public function set(array $values)
    {
        parent::set($values);

        return $this->resetKeys();
    }

    /**
     * Add Entity to collection
     *
     * @param EntityInterface $entity
     *
     * @return $this
     */
    public function add(EntityInterface $entity)
    {
        $this->values[] = $entity;

        return $this;
    }
}