<?php

namespace SimpleCollection;

/**
 * Collection, will reset value keys on construct
 *
 * @copyright Felix Buchheim
 * @author    Felix Buchheim <hanibal4nothing@gmail.com>
 */
class ArrayCollection extends AbstractCollection
{

    /**
     * ArrayCollection constructor.
     *
     * @param array $aValues
     */
    public function __construct(array $aValues = array())
    {
        parent::__construct($aValues);
        $this->resetKeys();
    }

    /**
     * add a entity to the collection
     *
     * @param mixed $mValue
     *
     * @return $this
     */
    public function add($mValue)
    {
        $this->values[] = $mValue;

        return $this;
    }

    /**
     * Set all values and reset keys
     *
     * @param array $aValues
     *
     * @return $this
     */
    public function set(array $aValues)
    {
        parent::set($aValues);

        return $this->resetKeys();
    }
}