<?php

namespace SimpleCollection\Entity;

use InvalidArgumentException;

/**
 * Associative collection
 *
 * @package SimpleCollection
 * @author  Felix Buchheim <hanibal4nothing@gmail.com>
 */
class EntityAssocCollection extends AbstractEntityCollection
{

    /**
     * EntityAssocCollection constructor.
     *
     * @param AssocEntityInterface[] $entities
     */
    public function __construct(array $entities = [])
    {
        $this->checkClasses($entities);
        parent::__construct($this->indexEntities($entities));
    }

    /**
     * Check if entity already exists
     *
     * @param AssocEntityInterface $entity
     *
     * @return bool
     */
    public function entityExists(AssocEntityInterface $entity): bool
    {
        return $this->offsetExists($entity->getCollectionIndex());
    }

    /**
     * Set indexed entities to collection
     *
     * @param AssocEntityInterface[] $entities
     *
     * @return $this
     */
    public function set(array $entities): static
    {
        return parent::set($this->indexEntities($entities));
    }

    /**
     * Add Entity to collection
     *
     * @param AssocEntityInterface $entity
     *
     * @return $this
     */
    public function add(mixed $entity): static
    {
        $this->checkClass($entity);
        $this->values[$entity->getCollectionIndex()] = $entity;

        return $this;
    }

    /**
     * Create index array
     *
     * @param AssocEntityInterface[] $entities
     *
     * @return array
     */
    protected function indexEntities(array $entities): array
    {
        $indexEntities = [];
        foreach ($entities as $entity) {
            $indexEntities[$entity->getCollectionIndex()] = $entity;
        }

        return $indexEntities;
    }

    /**
     * Check class
     *
     * @param AssocEntityInterface $entity
     *
     * @return $this
     */
    protected function checkClass(mixed $entity): static
    {
        if (false === $entity instanceof AssocEntityInterface) {
            throw new InvalidArgumentException('Expect entity of class \SimpleCollection\Entity\AssocEntityInterface');
        }

        return $this;
    }
}
