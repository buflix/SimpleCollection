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
    public function __construct($aEntities = array())
    {
        parent::__construct($aEntities);
        $this->resetKeys();
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