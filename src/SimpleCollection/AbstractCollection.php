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
            $sKey     = key($this->values);
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
     * set the value by the given offset
     *
     * @param string|int $mOffset Offset
     * @param mixed      $mValue  ProxyServer
     *
     * @throws \InvalidArgumentException
     */
    public function offsetSet($mOffset, $mValue)
    {
        if (false === isset($mOffset)) {
            throw new \InvalidArgumentException('Offset can not be null');
        }
        $this->values[$mOffset] = $mValue;
    }

    /**
     * unset the value by the offset
     *
     * @param string|int $mOffset Offset
     *
     * @return void
     */
    public function offsetUnset($mOffset)
    {
        unset($this->values[$mOffset]);
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
     * seek the pointer to the offset
     *
     * @param int|string $iOffset Seek position
     *
     * @throws \OutOfBoundsException
     */
    public function seek($iOffset)
    {
        $this->rewind();
        $iPosition = 0;

        while ($iPosition < $iOffset and $this->valid()) {
            $this->next();
            $iPosition++;
        }

        if (false === $this->valid()) {
            throw new \OutOfBoundsException('Invalid seek position');
        }
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
}