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
        foreach ($aEntities as $oEntity) {
            $this->checkClass($oEntity);
        }
        parent::__construct($aEntities);
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
        $this->checkClass($oEntity);
        parent::offsetSet($mOffset, $oEntity);
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
     * Check if the given object is class of EntityInterface
     *
     * @param EntityInterface $oEntity
     *
     * @return $this
     */
    protected function checkClass($oEntity)
    {
        if (false === $oEntity instanceof EntityInterface) {
            throw new \InvalidArgumentException(
                'Expect entity of class \SimpleCollection\Entity\EntityInterface. ' . get_class($oEntity) . ' given'
            );
        }

        return $this;
    }
}