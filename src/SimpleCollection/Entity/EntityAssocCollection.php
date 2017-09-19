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
    public function __construct(array $entities = array())
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
    public function entityExists(AssocEntityInterface $entity)
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
    public function set(array $entities)
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
    public function add(AssocEntityInterface $entity)
    {
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
    protected function indexEntities(array $entities)
    {
        $indexEntities = array();
        foreach ($entities as $entity) {
            $indexEntities[$entity->getCollectionIndex()] = $entity;
        }

        return $indexEntities;
    }
}