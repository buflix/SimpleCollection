<?php

namespace SimpleCollection;

use SimpleCollection\Base\ScCollection;

/**
 * Collection, will reset value keys on construct
 *
 * @copyright Felix Buchheim
 * @author    Felix Buchheim <hanibal4nothing@gmail.com>
 */
class ListCollection extends ScCollection
{

    /**
     * ArrayCollection constructor.
     *
     * @param array $values
     */
    public function __construct(array $values = array())
    {
        parent::__construct($values);
        $this->resetKeys();
    }

    /**
     * add a value to the collection
     *
     * @param mixed $value
     *
     * @return $this
     */
    public function add($value)
    {
        $this->values[] = $value;

        return $this;
    }

    /**
     * Set all values and reset keys
     *
     * @param array $values
     *
     * @return $this
     */
    public function set(array $values)
    {
        parent::set($values);

        return $this->resetKeys();
    }
}