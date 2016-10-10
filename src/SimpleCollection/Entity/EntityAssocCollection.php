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
     * @param AssocEntityInterface[] $aEntities
     */
    public function __construct(array $aEntities = array())
    {
        $this->checkClasses($aEntities);
        parent::__construct($this->indexEntities($aEntities));
    }

    /**
     * Check if entity already exists
     *
     * @param AssocEntityInterface $oEntity
     *
     * @return bool
     */
    public function entityExists(AssocEntityInterface $oEntity)
    {
        return $this->offsetExists($oEntity->getCollectionIndex());
    }

    /**
     * Set indexed entities to collection
     *
     * @param AssocEntityInterface[] $aEntities
     *
     * @return $this
     */
    public function set(array $aEntities)
    {
        return parent::set($this->indexEntities($aEntities));
    }

    /**
     * Add Entity to collection
     *
     * @param AssocEntityInterface $oEntity
     *
     * @return $this
     */
    public function add($oEntity)
    {
        $this->checkClass($oEntity);
        $this->values[$oEntity->getCollectionIndex()] = $oEntity;

        return $this;
    }

    /**
     * Create index array
     *
     * @param AssocEntityInterface[] $aEntities
     *
     * @return array
     */
    protected function indexEntities(array $aEntities)
    {
        $aIndexEntities = array();
        foreach ($aEntities as $oEntity) {
            $aIndexEntities[$oEntity->getCollectionIndex()] = $oEntity;
        }

        return $aIndexEntities;
    }

    /**
     * Check class
     *
     * @param AssocEntityInterface $oEntity
     *
     * @return $this
     */
    protected function checkClass($oEntity)
    {
        if (false === $oEntity instanceof AssocEntityInterface) {
            throw new \InvalidArgumentException(
                'Expect entity of class \SimpleCollection\Entity\AssocEntityInterface. '
                . get_class($oEntity) . ' given'
            );
        }

        return $this;
    }
}