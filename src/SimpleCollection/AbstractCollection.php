<?php
namespace SimpleCollection;

/**
 * abstract collection
 *
 * @copyright Felix Buchheim
 * @author    Felix Buchheim <hanibal4nothing@gmail.com>
 */
abstract class AbstractCollection implements \Countable, \ArrayAccess, \SeekableIterator
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
     * @param array $aValues
     */
    public function __construct(array $aValues = array())
    {
        $this->values = $aValues;
    }

    /**
     * returns current value
     *
     * @return mixed
     */
    public function current()
    {
        $sKey = key($this->values);

        return (true === isset($this->values[$sKey])) ? $this->values[$sKey] : null;
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
        $mValue = $this->next();

        return (false === $mValue and false === $this->valid()) ? self::NOT_SET_FLAG : $mValue;
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
        $mValue = $this->prev();

        return (false === $mValue and false === $this->valid()) ? self::NOT_SET_FLAG : $mValue;
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
        $bIsValid = false;
        if (null !== $this->values) {
            $sKey     = $this->key();
            $bIsValid = isset($sKey);
        }

        return $bIsValid;
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
     * @param string|int $mOffset Offset
     *
     * @return bool
     */
    public function offsetExists($mOffset)
    {
        return isset($this->values[$mOffset]);
    }

    /**
     * return the value with the given offset
     *
     * @param string|int $mOffset Offset
     *
     * @return mixed
     */
    public function offsetGet($mOffset)
    {
        return $this->values[$mOffset];
    }

    /**
     * Return the value with the given offset, if is not set return the default
     *
     * @param mixed $mOffset
     * @param mixed $mDefault
     *
     * @return mixed
     */
    public function get($mOffset, $mDefault = null)
    {
        return (true === isset($this->values[$mOffset])) ? $this->values[$mOffset] : $mDefault;
    }

    /**
     * Set all values
     *
     * @param array $aValues
     *
     * @return $this
     */
    public function set(array $aValues)
    {
        $this->values = $aValues;

        return $this;
    }

    /**
     * set the value by the given offset
     *
     * @param string|int $mOffset Offset
     * @param mixed      $mValue  ProxyServer
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function offsetSet($mOffset, $mValue)
    {
        if (false === is_string($mOffset) and false === is_integer($mOffset)) {
            throw new \InvalidArgumentException('Invalid offset given: ' . gettype($mOffset));
        }
        $this->values[$mOffset] = $mValue;

        return $this;
    }

    /**
     * unset the value by the offset
     *
     * @param string|int $mOffset Offset
     *
     * @return $this
     */
    public function offsetUnset($mOffset)
    {
        unset($this->values[$mOffset]);

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
     * @param int $iOffset Seek position
     *
     * @return mixed
     * @throws \OutOfBoundsException
     */
    public function seek($iOffset)
    {
        $this->rewind();
        $iPosition = 0;

        while ($iPosition < $iOffset and true === $this->valid()) {
            $this->next();
            $iPosition++;
        }

        if (false === $this->valid()) {
            throw new \OutOfBoundsException('Invalid seek position: ' . $iOffset);
        }

        return $this->current();
    }

    /**
     * Try to seek to given offset
     *
     * @param mixed $mKey
     * @param bool  $bStrictMode
     *
     * @return mixed
     */
    public function seekToKey($mKey, $bStrictMode = true)
    {
        $this->rewind();
        if ($bStrictMode === true) {
            while (true === $this->valid() and $mKey !== $this->key()) {
                $this->next();
            }
        } else {
            while (true === $this->valid() and $mKey != $this->key()) {
                $this->next();
            }
        }
        if (false === $this->valid()) {
            throw new \OutOfBoundsException('Invalid seek position: ' . $mKey);
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
        return true === empty($this->values);
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
     * @param \Closure $cClosure
     *
     * @return AbstractCollection
     */
    public function filter(\Closure $cClosure)
    {
        $sClassName      = get_class($this);
        $aFilteredValues = array();
        foreach ($this->values as $sKey => $mValue) {
            if (true === $cClosure($mValue, $sKey)) {
                $aFilteredValues[$sKey] = $mValue;
            }
        }

        return new $sClassName($aFilteredValues);
    }

    /**
     * Use a function on all values of the collection, and set the result as new values for the key
     *
     * @param \Closure $cClosure
     *
     * @return $this
     */
    public function forAll(\Closure $cClosure)
    {
        foreach ($this->values as $mKey => $mValue) {
            $this->offsetSet($mKey, $cClosure($mValue, $mKey));
        }

        return $this;
    }

    /**
     * Slice elements and create a new instance
     *
     * @param mixed $mStartKey
     * @param bool  $bStrict
     * @param int   $iLength
     *
     * @return AbstractCollection
     */
    public function sliceByKey($mStartKey, $bStrict = true, $iLength = PHP_INT_MAX)
    {
        $aSlice = array();
        try {
            $this->seekToKey($mStartKey, $bStrict);

            if ($iLength > 0) {
                $aSlice[$this->key()] = $this->current();
                $iLength--;
                $this->next();
            }

            while ($iLength > 0 and true === $this->valid()) {
                $aSlice[$this->key()] = $this->current();
                $iLength--;
                $this->next();
            }
        } catch (\OutOfBoundsException $e) {
            //do nothing if key not exists
        }
        $sClassName = get_class($this);

        return new $sClassName($aSlice);
    }
}