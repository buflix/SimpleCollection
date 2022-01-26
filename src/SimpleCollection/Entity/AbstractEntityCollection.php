<?php

namespace SimpleCollection\Entity;

use InvalidArgumentException;
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
    public function __construct(array $entities = [])
    {
        $this->checkClasses($entities);
        parent::__construct($entities);
    }

    /**
     * set the entity by the given offset
     *
     * @param string|int|null $offset Offset
     * @param EntityInterface $entity ProxyServer
     *
     * @return $this
     * @throws InvalidArgumentException
     */
    public function offsetSet($offset, $entity): static
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
     * @throws InvalidArgumentException
     */
    public function set(array $entities): static
    {
        $this->checkClasses($entities);

        return parent::set($entities);
    }

    /**
     * return this collection as array
     *
     * @return array
     */
    public function toArray(): array
    {
        $values = [];

        foreach ($this->values as $key => $entity) {
            /* @var EntityInterface $entity */
            $values[$key] = $entity->toArray();
        }

        return $values;
    }

    /**
     * Check all classes of given array
     *
     * @param EntityInterface[] $entities
     *
     * @return $this
     * @throws InvalidArgumentException
     */
    public function checkClasses(array $entities): static
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
    public function add(mixed $entity): static
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
     * @throws InvalidArgumentException
     */
    protected function checkClass(mixed $entity): static
    {
        if (false === $entity instanceof EntityInterface) {
            throw new InvalidArgumentException('Expect entity of class \SimpleCollection\Entity\EntityInterface');
        }

        return $this;
    }

    /**
     * @return array
     * @see \JsonSerializable
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
