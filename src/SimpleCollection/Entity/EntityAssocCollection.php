<?php

namespace SimpleCollection\Entity;

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
     * @param AssocEntityInterface[] $values
     *
     * @return $this
     */
    public function set(array $values): static
    {
        return parent::set($this->indexEntities($values));
    }

    /**
     * Add Entity to collection
     *
     * @param AssocEntityInterface|EntityInterface $entity
     *
     * @return $this
     */
    public function add(AssocEntityInterface|EntityInterface $entity): static
    {
        if ($entity instanceof AssocEntityInterface) {
            $this->values[$entity->getCollectionIndex()] = $entity;
        } else {
            parent::add($entity);
        }

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
}
