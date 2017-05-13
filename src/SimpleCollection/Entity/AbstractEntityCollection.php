<?php

namespace SimpleCollection\Entity;

use SimpleCollection\AbstractCollection;

/**
 * abstract collection
 *
 * @copyright Felix Buchheim
 * @author    Felix Buchheim <hanibal4nothing@gmail.com>
 */
abstract class AbstractEntityCollection extends AbstractCollection implements EntityInterface
{

    /**
     * AbstractCollection constructor
     *
     * set the values
     * use the offsetSet to check the type
     *
     * @param EntityInterface[] $entities
     */
    public function __construct(array $entities = array())
    {
        $this->checkClasses($entities);
        parent::__construct($entities);
    }

    /**
     * set the entity by the given offset
     *
     * @param string|int      $offset Offset
     * @param EntityInterface $entity ProxyServer
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function offsetSet($offset, $entity)
    {
        $this->checkClass($entity);

        return parent::offsetSet($offset, $entity);
    }

    /**
     * Set all entities
     *
     * @param EntityInterface[] $entities
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function set(array $entities)
    {
        $this->checkClasses($entities);

        return parent::set($entities);
    }

    /**
     * return this collection as array
     *
     * @return array
     */
    public function toArray()
    {
        $return = array();

        foreach ($this->values as $key => $entity) {
            /* @var EntityInterface $entity */
            $return[$key] = $entity->toArray();
        }

        return $return;
    }

    /**
     * Check all classes of given array
     *
     * @param EntityInterface[] $entities
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function checkClasses(array $entities)
    {
        foreach ($entities as $entity) {
            $this->checkClass($entity);
        }

        return $this;
    }

    /**
     * Add Entity to collection
     *
     * @param EntityInterface $entity
     *
     * @return $this
     */
    public function add($entity)
    {
        $this->checkClass($entity);
        $this->values[] = $entity;

        return $this;
    }

    /**
     * Check if the given object is class of EntityInterface
     *
     * @param EntityInterface $entity
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    protected function checkClass($entity)
    {
        if (false === $entity instanceof EntityInterface) {
            throw new \InvalidArgumentException('Expect entity of class \SimpleCollection\Entity\EntityInterface');
        }

        return $this;
    }
}