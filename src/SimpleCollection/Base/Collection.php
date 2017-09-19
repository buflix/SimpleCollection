<?php

namespace SimpleCollection\Base;

use SimpleCollection\Entity\EntityInterface;

/**
 * Collection Base collection
 *
 * @copyright Felix Buchheim
 * @author    Felix Buchheim <hanibal4nothing@gmail.com>
 */
class Collection extends ArraySeekableCollection implements \JsonSerializable, EntityInterface
{
    /**
     * Magic Setter
     *
     * @param string|int $key
     * @param mixed $value
     *
     * @return $this
     */
    public function __set($key, $value)
    {
        $this->offsetSet($key, $value);

        return $this;
    }

    /**
     * Magic getter
     *
     * @param string|int $key
     *
     * @return $this
     */
    public function __get($key)
    {
        return $this->offsetGet($key);
    }

    /**
     * Check if key exists / value
     *
     * @param string|int $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return $this->offsetExists($name);
    }

    /**
     * Unset value by key
     *
     * @param string|int $name
     *
     * @return $this
     */
    public function __unset($name)
    {
        return $this->offsetUnset($name);
    }

    /**
     * Return this collection as string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
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
            if ($closure($value, $key)) {
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
    public function updateItems(\Closure $closure)
    {
        foreach ($this->values as $key => $value) {
            $this->offsetSet($key, $closure($key, $value));
        }

        return $this;
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
            $closure($key, $value);
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
     * @see EntityInterface::toArray()
     *
     * @return array
     */
    public function toArray()
    {
        return $this->getAll();
    }

    /**
     * @see \JsonSerializable::jsonSerialize()
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Return this collection as json string
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this, $options);
    }
}