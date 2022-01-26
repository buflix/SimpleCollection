<?php

namespace SimpleCollection\Entity;

use JsonSerializable;

/**
 * abstract entity
 *
 * @copyright Felix Buchheim
 * @author    Felix Buchheim <hanibal4nothing@gmail.com>
 */
abstract class AbstractEntity implements EntityInterface, JsonSerializable
{

    /**
     * return this entity as array
     *
     * @return array
     * @see EntityInterface::toArray
     */
    public function toArray(): array
    {
        $values = [];

        foreach (get_object_vars($this) as $key => $value) {
            if (is_object($value) and true === method_exists($value, 'toArray')) {
                $values[$key] = $value->toArray();
            } else {
                $values[$key] = $value;
            }
        }

        return $values;
    }

    /**
     * @return array
     * @see \JsonSerializable
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
