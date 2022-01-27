<?php

namespace SimpleCollection\Service\Pagination;

/**
 * Interface for PaginationCollections
 *
 * @copyright Felix Buchheim
 * @author    Felix Buchheim <hanibal4nothing@gmail.com>
 */
interface PaginationCollectionInterface
{

    /**
     * Seek the pointer to the offset position
     *
     * @param int $offset
     *
     * @return mixed
     */
    public function seek(int $offset): mixed;

    /**
     * Return the current key
     *
     * @return string|int|null
     */
    public function key(): string|int|null;

    /**
     * Improve the pointer and return the current value
     *
     * @return mixed
     */
    public function scNext(): mixed;

    /**
     * Return the count of current values in the collection
     *
     * @return int
     */
    public function count(): int;
}
