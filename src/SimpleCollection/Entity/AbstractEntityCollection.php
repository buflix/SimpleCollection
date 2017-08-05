<?php
namespace SimpleCollection\Entity;

use SimpleCollection\AbstractCollection;

/**
 * abstract collection
 *
 * @copyright Felix Buchheim
 * @author    Felix Buchheim <hanibal4nothing@gmail.com>
 */
abstract class AbstractEntityCollection extends AbstractCollection implements EntityInterface
{

    /**
     * AbstractCollection constructor
     *
     * set the values
     * use the offsetSet to check the type
     *
     * @param EntityInterface[] $aEntities
     */
    public function __construct(array $aEntities = array())
    {
        $this->checkClasses($aEntities);
        parent::__construct($aEntities);
    }

    /**
     * set the entity by the given offset
     *
     * @param string|int      $mOffset Offset
     * @param EntityInterface $oEntity ProxyServer
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function offsetSet($mOffset, $oEntity)
    {
        $this->checkClass($oEntity);

        return parent::offsetSet($mOffset, $oEntity);
    }

    /**
     * Set all entities
     *
     * @param EntityInterface[] $aEntities
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function set(array $aEntities)
    {
        $this->checkClasses($aEntities);

        return parent::update($aEntities);
    }

    /**
     * return this collection as array
     *
     * @return array
     */
    public function toArray()
    {
        $aReturn = array();

        foreach ($this->values as $sKey => $oEntity) {
            /* @var EntityInterface $oEntity */
            $aReturn[$sKey] = $oEntity->toArray();
        }

        return $aReturn;
    }

    /**
     * Check all classes of given array
     *
     * @param EntityInterface[] $aEntities
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function checkClasses(array $aEntities)
    {
        foreach ($aEntities as $oEntity) {
            $this->checkClass($oEntity);
        }

        return $this;
    }

    /**
     * Add Entity to collection
     *
     * @param EntityInterface $oEntity
     *
     * @return $this
     */
    public function add($oEntity)
    {
        $this->checkClass($oEntity);
        $this->values[] = $oEntity;

        return $this;
    }

    /**
     * Check if the given object is class of EntityInterface
     *
     * @param EntityInterface $oEntity
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    protected function checkClass($oEntity)
    {
        if (false === $oEntity instanceof EntityInterface) {
            throw new \InvalidArgumentException('Expect entity of class \SimpleCollection\Entity\EntityInterface');
        }

        return $this;
    }
}