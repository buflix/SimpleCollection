<?php
namespace SimpleCollection\Entity;

/**
 * abstract entity
 *
 * @copyright Felix Buchheim
 * @author    Felix Buchheim <hanibal4nothing@gmail.com>
 */
abstract class AbstractEntity implements EntityInterface
{

    /**
     * return this entity as array
     *
     * @see EntityInterface::toArray
     * @return array
     */
    public function toArray()
    {
        $return = array();

        foreach (get_object_vars($this) as $key => $value) {
            if ($value instanceof EntityInterface || method_exists($value, 'toArray')) {
                $return[$key] = $value->toArray();
            } else {
                $return[$key] = $value;
            }
        }

        return $return;
    }
}