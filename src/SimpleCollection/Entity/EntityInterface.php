<?php

namespace SimpleCollection\Entity;

/**
 * entity Interface
 *
 * @copyright Felix Buchheim
 * @author    Felix Buchheim <hanibal4nothing@gmail.com>
 */
interface EntityInterface
{
    /**
     * return the Entity as array
     *
     * @return array
     */
    public function toArray(): array;
}
