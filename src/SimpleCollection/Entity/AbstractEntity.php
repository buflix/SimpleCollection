<?php
namespace SimpleCollection\Entity;

/**
 * abstract entity
 *
 * @copyright Felix Buchheim
 * @author    Felix Buchheim <hanibal4nothing@gmail.com>
 */
abstract class AbstractEntity implements EntityInterface, \JsonSerializable
{

    /**
     * return this entity as array
     *
     * @see EntityInterface::toArray
     * @return array
     */
    public function toArray()
    {
        $aReturn = array();

        foreach (get_object_vars($this) as $sKey => $mValue) {
            if (is_object($mValue) and true === method_exists($mValue, 'toArray')) {
                $aReturn[$sKey] = $mValue->toArray();
            } else {
                $aReturn[$sKey] = $mValue;
            }
        }

        return $aReturn;
    }

    /**
     * @see \JsonSerializable
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}