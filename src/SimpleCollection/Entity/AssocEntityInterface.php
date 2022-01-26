<?php
namespace SimpleCollection\Entity;

/**
 * entity Interface
 *
 * @copyright Felix Buchheim
 * @author    Felix Buchheim <hanibal4nothing@gmail.com>
 */
interface AssocEntityInterface extends EntityInterface
{

    /**
     * Return the value to index in assocCollection
     *
     * @return string|integer
     */
    public function getCollectionIndex(): int|string;
}
