<?php

namespace SimpleCollection\Service\Pagination;

/**
 * Interface for PaginationCollections
 *
 * @copyright Felix Buchheim
 * @author    Felix Buchheim <hanibal4nothing@gmail.com>
 */
interface PaginationCollectionInterface extends \Countable, \SeekableIterator
{

    /**
     * Seek the pointer to the offset position
     *
     * @param mixed $offset
     *
     * @return mixed
     */
    public function seek($offset);

    /**
     * Return the current key
     *
     * @return mixed
     */
    public function key();

    /**
     * Improve the pointer and return the current value
     *
     * @return mixed
     */
    public function scNext();

    /**
     * Return the count of current values in the collection
     *
     * @return int
     */
    public function count();
}