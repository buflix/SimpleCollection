<?php
namespace SimpleCollection;

/**
 * abstract collection
 *
 * @copyright Felix Buchheim
 * @author    Felix Buchheim <hanibal4nothing@gmail.com>
 */
class AbstractCollection implements \Countable, \ArrayAccess, \SeekableIterator, EntityInterface
{
    /**
     * array with entities
     *
     * @var EntityInterface[]
     */
    protected $entities = array();

    /**
     * AbstractCollection constructor
     *
     * set the entities
     * use the add to check the type
     *
     * @param array $aEntities
     */
    public function __construct(array $aEntities = array())
    {
        foreach ($aEntities as $sKey => $oEntity) {
            $this->offsetSet($sKey, $oEntity);
        }
    }

    /**
     * returns current object
     *
     * @return EntityInterface
     */
    public function current()
    {
        $sKey = key($this->entities);

        return (true === isset($this->entities[$sKey])) ? $this->entities[$sKey] : null;
    }

    /**
     * improve the pointer
     *
     * @return EntityInterface|false
     */
    public function next()
    {
        return next($this->entities);
    }

    /**
     * return the current key
     *
     * @return int
     */
    public function key()
    {
        return key($this->entities);
    }

    /**
     * check if the the current object is set
     *
     * @return bool True if set, otherwise false
     */
    public function valid()
    {
        $bIsValid = false;
        if (null !== $this->entities) {
            $sKey     = key($this->entities);
            $bIsValid = isset($sKey);
        }

        return $bIsValid;
    }

    /**
     * return the first element in collection
     *
     * @return EntityInterface|false
     */
    public function rewind()
    {
        return reset($this->entities);
    }

    /**
     * return the last element in collection
     *
     * @return EntityInterface|false
     */
    public function end()
    {
        return end($this->entities);
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
        return isset($this->entities[$mOffset]);
    }

    /**
     * return the entity with the given offset
     *
     * @param string|int $mOffset Offset
     *
     * @return EntityInterface
     */
    public function offsetGet($mOffset)
    {
        return $this->entities[$mOffset];
    }

    /**
     * set the entity by the given offset
     *
     * @param string|int      $mOffset Offset
     * @param EntityInterface $oEntity ProxyServer
     *
     * @throws \InvalidArgumentException
     */
    public function offsetSet($mOffset, $oEntity)
    {
        if (false === ($oEntity instanceof EntityInterface)) {
            throw new \InvalidArgumentException('Entity is no instance of AbstractEntity');
        }
        if (null === $mOffset) {
            $this->entities[] = $oEntity;
        }
        else {
            $this->entities[$mOffset] = $oEntity;
        }
    }

    /**
     * add a entity to the collection
     *
     * @param EntityInterface $oEntity
     */
    public function add(EntityInterface $oEntity)
    {
        $this->entities[] = $oEntity;
    }

    /**
     * unset the entity by the offset
     *
     * @param string|int $mOffset Offset
     *
     * @return void
     */
    public function offsetUnset($mOffset)
    {
        unset($this->entities[$mOffset]);
    }

    /**
     * count the current entities in the collection
     *
     * @return int
     */
    public function count()
    {
        return count($this->entities);
    }

    /**
     * seek the pointer the the offset
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
     * reset the collection keys
     *
     * @return $this
     */
    public function resetKeys()
    {
        $this->entities = array_values($this->entities);

        return $this;
    }

    /**
     * return this collection as array
     *
     * @return array
     */
    public function toArray()
    {
        $aReturn = array();

        foreach ($this->entities as $sKey => $oEntity) {
            $aReturn[$sKey] = $oEntity->toArray();
        }

        return $aReturn;
    }
}