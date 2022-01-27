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
        parent::__construct($entities);
    }

    /**
     * set the entity by the given offset
     *
     * @param string|int|null $offset Offset
     * @param EntityInterface $value ProxyServer
     *
     * @return $this
     * @throws InvalidArgumentException
     */
    public function offsetSet($offset, mixed $value): static
    {
        return parent::offsetSet($offset, $value);
    }

    /**
     * Set all entities
     *
     * @param EntityInterface[] $values
     *
     * @return $this
     * @throws InvalidArgumentException
     */
    public function set(array $values): static
    {
        return parent::set($values);
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
     * Add Entity to collection
     *
     * @param EntityInterface $entity
     *
     * @return $this
     */
    public function add(EntityInterface $entity): static
    {
        $this->values[] = $entity;

        return $this;
    }

    /**
     * @return mixed
     * @see \JsonSerializable
     */
    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
