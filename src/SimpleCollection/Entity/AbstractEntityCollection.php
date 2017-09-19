<?php

namespace SimpleCollection\Entity;

use SimpleCollection\Base\ScCollection;

/**
 * abstract collection
 *
 * @copyright Felix Buchheim
 * @author    Felix Buchheim <hanibal4nothing@gmail.com>
 */
abstract class AbstractEntityCollection extends ScCollection implements EntityInterface
{

    /**
     * return this collection as array
     *
     * @return array
     */
    public function toArray()
    {
        $return = array();

        foreach ($this->values as $key => $entity) {
            /* @var EntityInterface $entity */
            $return[$key] = $entity->toArray();
        }

        return $return;
    }
}