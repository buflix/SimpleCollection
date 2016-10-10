<?php

namespace SimpleCollection\Entity;

/**
 * Array collection
 *
 * @package SimpleCollection
 * @author  Felix Buchheim <hanibal4nothing@gmail.com>
 */
class EntityArrayCollection extends AbstractEntityCollection
{

    /**
     * EntityArrayCollection constructor.
     *
     * @param array|EntityInterface[] $aEntities
     */
    public function __construct($aEntities)
    {
        parent::__construct($aEntities);
        $this->resetKeys();
    }

    /**
     * add a entity to the collection
     *
     * @param EntityInterface $oEntity
     *
     * @return $this
     */
    public function add(EntityInterface $oEntity)
    {
        $this->values[] = $oEntity;

        return $this;
    }

    /**
     * Set all values and reset keys
     *
     * @param EntityInterface[] $aValues
     *
     * @return $this
     */
    public function set(array $aValues)
    {
        parent::set($aValues);

        return $this->resetKeys();
    }
}