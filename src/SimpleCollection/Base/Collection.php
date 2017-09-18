<?php

namespace SimpleCollection\Base;

/**
 * Collection Base collection
 *
 * @copyright Felix Buchheim
 * @author    Felix Buchheim <hanibal4nothing@gmail.com>
 */
class Collection extends ArraySeekableCollection implements \JsonSerializable
{

    /**
     * rewind the pointer for one position
     *
     * @return mixed|false
     */
    public function prev()
    {
        return prev($this->values);
    }

    /**
     * return the last element in collection
     *
     * @return mixed|false
     */
    public function end()
    {
        return end($this->values);
    }

    /**
     * Return the value with the given offset, if is not set return the default
     *
     * @param mixed $offset
     * @param mixed $default
     *
     * @return mixed
     */
    public function get($offset, $default = null)
    {
        return (isset($this->values[$offset])) ? $this->values[$offset] : $default;
    }

    /**
     * Set all values
     *
     * @param array $values
     *
     * @return $this
     */
    public function set(array $values)
    {
        $this->values = $values;

        return $this;
    }

    /**
     * Try to seek to given offset
     *
     * @param mixed $key
     * @param bool  $strictMode
     *
     * @return mixed
     * @throws \OutOfBoundsException
     */
    public function seekToKey($key, $strictMode = true)
    {
        $this->rewind();
        if ($strictMode) {
            while ($this->valid() && $key !== $this->key()) {
                $this->next();
            }
        } else {
            while ($this->valid() && $key != $this->key()) {
                $this->next();
            }
        }
        if (!$this->valid()) {
            throw new \OutOfBoundsException('Invalid seek position: ' . $key);
        }

        return $this->current();
    }

    /**
     * return if the collection is empty
     *
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->values);
    }

    /**
     * Return all values
     *
     * @return array
     */
    public function getAll()
    {
        return $this->values;
    }

    /**
     * reset the collection keys
     *
     * @return $this
     */
    public function resetKeys()
    {
        $this->values = array_values($this->values);

        return $this;
    }

    /**
     * Clear Collection
     *
     * @return $this
     */
    public function clear()
    {
        $this->values = array();
        $this->rewind();

        return $this;
    }

    /**
     * Return all used keys
     *
     * @return array
     */
    public function getKeys()
    {
        return array_keys($this->values);
    }

    /**
     * Filters the current values and return a new collection
     *
     * @param \Closure $closure
     *
     * @return Collection
     */
    public function filter(\Closure $closure)
    {
        $className      = get_class($this);
        $filteredValues = array();
        foreach ($this->values as $key => $value) {
            if (true === $closure($value, $key)) {
                $filteredValues[$key] = $value;
            }
        }

        return new $className($filteredValues);
    }

    /**
     * Use a function on all values of the collection, and set the result as new values for the key
     *
     * @param \Closure $closure
     *
     * @return $this
     */
    public function forAll(\Closure $closure)
    {
        foreach ($this->values as $key => $value) {
            $this->offsetSet($key, $closure($value, $key));
        }

        return $this;
    }

    /**
     * Slice elements and create a new instance
     *
     * @param mixed $startKey
     * @param bool  $strict
     * @param int   $length
     *
     * @return Collection
     */
    public function sliceByKey($startKey, $strict = true, $length = PHP_INT_MAX)
    {
        $slice = array();
        try {
            $this->seekToKey($startKey, $strict);

            if ($length > 0) {
                $slice[$this->key()] = $this->current();
                $length--;
                $this->next();
            }

            while ($length > 0 && $this->valid()) {
                $slice[$this->key()] = $this->current();
                $length--;
                $this->next();
            }
        }
        catch (\OutOfBoundsException $e) {
            //do nothing if key not exists
        }
        $className = get_class($this);

        return new $className($slice);
    }

    /**
     * @see \JsonSerializable::jsonSerialize()
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->getAll();
    }

}