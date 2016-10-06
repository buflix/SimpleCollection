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
    public function __construct(array $aEntities)
    {
        parent::__construct($this->indexEntities($aEntities));
    }

    /**
     * Check if entity already exists
     *
     * @param AssocEntityInterface $oEntity
     *
     * @return bool
     */
    public function entityExist(AssocEntityInterface $oEntity)
    {
        return $this->offsetExists($oEntity->getCollectionIndex());
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
}