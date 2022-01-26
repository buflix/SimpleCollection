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
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        parent::__construct($values);
        $this->resetKeys();
    }

    /**
     * add an entity to the collection
     *
     * @param mixed $value
     *
     * @return $this
     */
    public function add(mixed $value): static
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
    public function set(array $values): static
    {
        parent::set($values);

        return $this->resetKeys();
    }
}
