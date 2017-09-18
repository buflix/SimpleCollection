<?php
namespace SimpleCollection;

use SimpleCollection\Service\Pagination\PaginationCollectionInterface;

/**
 * abstract collection
 *
 * @copyright Felix Buchheim
 * @author    Felix Buchheim <hanibal4nothing@gmail.com>
 * @author    Willi Eßer <willi.esser@troublete.com>
 */
abstract class AbstractCollection implements \Countable, \ArrayAccess, \SeekableIterator, PaginationCollectionInterface, \JsonSerializable
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
     * Method to retrieve the value at the current pointer position
     *
     * @return mixed|null
     */
    public function current()
    {
        $sKey = $this->key();
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
     * Like next, but return self::Not_Set_Flag if there are no next value instead of false
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
        return $this->key() !== null;
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
     * Method to reset all values
     * @param array $aValues
     * @return AbstractCollection
     */
    public function update(array $aValues)
    {
        $modifiedCollection = clone $this;
        $modifiedCollection->values = $aValues;
        return $modifiedCollection;
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
        if (false === is_string($mOffset) and false === is_integer($mOffset)) {
            throw new \InvalidArgumentException('Invalid offset given: ' . gettype($mOffset));
        }

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
     * @param string|int $mKey
     * @param bool  $bStrictMode
     *
     * @return mixed
     * @throws \OutOfBoundsException
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
        return $this->values === [] || !isset($this->values);
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
     * Produces an immutable version of the list and filters for values
     * @param \Closure $cClosure
     * @return AbstractCollection
     */
    public function filter(\Closure $cClosure)
    {
        $modifiedCollection = clone $this;
        foreach ($modifiedCollection->values as $sKey => $mValue) {
            if (false === call_user_func($cClosure, $mValue, $sKey)) {
                unset($modifiedCollection->values[$sKey]);
            }
        }
        return $modifiedCollection;
    }

    /**
     * Method too apply a closure to all values available. Will return an immutable state.
     * @param \Closure $cClosure
     * @return AbstractCollection
     */
    public function forAll(\Closure $cClosure)
    {
        $modifiedCollection = clone $this;
        foreach ($modifiedCollection->values as $mKey => $mValue) {
            $modifiedCollection->offsetSet($mKey, $cClosure($mValue, $mKey));
        }
        return $modifiedCollection;
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

    /**
     * @see \JsonSerializable
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->getAll();
    }

}