<?php

namespace SimpleCollection\Base;

/**
 * Collection with default collection methods
 *
 * @author    Felix Buchheim <hanibal4nothing@gmail.com>
 * @copyright Felix Buchheim
 */
class ArraySeekableCollection implements \Countable, \SeekableIterator, \ArrayAccess
{
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
     * count the current entities in the collection
     *
     * @return int
     */
    public function count()
    {
        return count($this->values);
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
     * set the value by the given offset
     *
     * @param string|int $offset Offset
     * @param mixed      $value  ProxyServer
     *
     * @return $this
     */
    public function offsetSet($offset, $value)
    {
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
     * improve the pointer
     *
     * @return mixed|false
     */
    public function next()
    {
        return next($this->values);
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
     * return the current key
     *
     * @return int
     */
    public function key()
    {
        return key($this->values);
    }
}