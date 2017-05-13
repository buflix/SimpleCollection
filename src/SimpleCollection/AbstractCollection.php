<?php

namespace SimpleCollection;

use SimpleCollection\Service\Pagination\PaginationCollectionInterface;

/**
 * abstract collection
 *
 * @copyright Felix Buchheim
 * @author    Felix Buchheim <hanibal4nothing@gmail.com>
 */
abstract class AbstractCollection implements \ArrayAccess, PaginationCollectionInterface
{

    /**
     * Flag if the value no existing
     *
     * To be able to distinguish existing values such as "null" or "false" from the information that the requested
     * value is not present
     *
     * @var string
     */
    const NOT_SET_FLAG = '<|__NOT_SET__|>';

    /**
     * array with values
     *
     * @var array
     */
    protected $values = array();

    /**
     * AbstractCollection constructor
     *
     * @param array $values
     */
    public function __construct(array $values = array())
    {
        $this->values = $values;
    }

    /**
     * returns current value
     *
     * @return mixed
     */
    public function current()
    {
        $key = key($this->values);

        return (isset($this->values[$key])) ? $this->values[$key] : null;
    }

    /**
     * improve the pointer
     *
     * @return mixed|false
     */
    public function next()
    {
        return next($this->values);
    }

    /**
     * Like next, but return Not_Set_Flag if there are no next value instead of false
     *
     * @return mixed
     */
    public function scNext()
    {
        $value = $this->next();

        return (false === $value && !$this->valid()) ? self::NOT_SET_FLAG : $value;
    }

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
     * rewind the pointer for one position
     *
     * @return mixed|false
     */
    public function scPrev()
    {
        $value = $this->prev();

        return (false === $value && !$this->valid()) ? self::NOT_SET_FLAG : $value;
    }

    /**
     * return the current key
     *
     * @return int
     */
    public function key()
    {
        return key($this->values);
    }

    /**
     * check if the the current value is set
     *
     * @return bool True if set, otherwise false
     */
    public function valid()
    {
        $isValid = false;
        if (isset($this->values)) {
            $key     = $this->key();
            $isValid = isset($key);
        }

        return $isValid;
    }

    /**
     * return the first element in collection
     *
     * @return mixed|false
     */
    public function rewind()
    {
        return reset($this->values);
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
     * check if the given offset exists
     *
     * @param string|int $offset Offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->values[$offset]);
    }

    /**
     * return the value with the given offset
     *
     * @param string|int $offset Offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->values[$offset];
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
     * set the value by the given offset
     *
     * @param string|int $offset Offset
     * @param mixed      $value  ProxyServer
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function offsetSet($offset, $value)
    {
        if (!is_string($offset) && !is_integer($offset)) {
            throw new \InvalidArgumentException('Invalid offset given: ' . gettype($offset));
        }
        $this->values[$offset] = $value;

        return $this;
    }

    /**
     * unset the value by the offset
     *
     * @param string|int $offset Offset
     *
     * @return $this
     */
    public function offsetUnset($offset)
    {
        unset($this->values[$offset]);

        return $this;
    }

    /**
     * count the current entities in the collection
     *
     * @return int
     */
    public function count()
    {
        return count($this->values);
    }

    /**
     * seek the pointer to the offset position
     *
     * @param int $offset Seek position
     *
     * @return mixed
     * @throws \OutOfBoundsException
     */
    public function seek($offset)
    {
        $this->rewind();
        $position = 0;

        while ($position < $offset && $this->valid()) {
            $this->next();
            $position++;
        }

        if (!$this->valid()) {
            throw new \OutOfBoundsException('Invalid seek position: ' . $offset);
        }

        return $this->current();
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
     * @return AbstractCollection
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
     * @param bool  $bStrict
     * @param int   $length
     *
     * @return AbstractCollection
     */
    public function sliceByKey($startKey, $bStrict = true, $length = PHP_INT_MAX)
    {
        $slice = array();
        try {
            $this->seekToKey($startKey, $bStrict);

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
}