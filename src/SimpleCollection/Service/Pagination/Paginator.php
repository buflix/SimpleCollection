<?php

namespace SimpleCollection\Service\Pagination;

use OutOfBoundsException;
use SimpleCollection\AbstractCollection;

/**
 * Paginator for collections
 *
 * @copyright Felix Buchheim
 * @author    Felix Buchheim <hanibal4nothing@gmail.com>
 */
class Paginator implements \Countable, \IteratorAggregate
{

    /**
     * Collection to pagination
     *
     * @var AbstractCollection
     */
    protected AbstractCollection $collection;

    /**
     * Index of first result
     *
     * @var int
     */
    protected int $page;

    /**
     * Amount of results
     *
     * @var int
     */
    protected int $itemsPerPage;

    /**
     * Paginator constructor.
     *
     * @param AbstractCollection $collection
     * @param int $page
     * @param int $itemsPerPage
     */
    public function __construct(AbstractCollection $collection, int $page = 1, int $itemsPerPage = 10)
    {
        $this->collection   = $collection;
        $this->page         = $page;
        $this->itemsPerPage = $itemsPerPage;
    }

    /**
     * @see \IteratorAggregate
     *
     * @throws OutOfBoundsException
     */
    public function getIterator(): AbstractCollection
    {
        $result = array();
        $start  = ($this->itemsPerPage * ($this->page - 1));
        if ($start < 0) {
            throw new OutOfBoundsException('Start index cant no be lower then 1');
        }
        try {
            $restOfResults                    = ($this->itemsPerPage - 1);
            $firstValues                      = $this->collection->seek($start);
            $result[$this->collection->key()] = $firstValues;
            while ($restOfResults > 0) {
                $value = $this->collection->scNext();
                if ($value === AbstractCollection::NOT_SET_FLAG) {
                    throw new OutOfBoundsException();
                }
                $result[$this->collection->key()] = $value;
                $restOfResults--;
            }

        } catch (OutOfBoundsException) {
            // do nothing, this exception is not critical
        }
        $className = get_class($this->collection);

        return new $className($result);
    }

    /**
     * @see \Countable
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->getIterator());
    }

    /**
     * @return int
     */
    public function fetchPageCount(): int
    {
        return (int) ceil($this->collection->count() / $this->itemsPerPage);
    }

    /**
     * The getter function for the property <em>$firstResult</em>.
     *
     * @return int
     */
    public function getFirstResult(): int
    {
        return $this->page;
    }

    /**
     * The setter function for the property <em>$firstResult</em>.
     *
     * @param int $firstResult
     *
     * @return $this Returns the instance of this class.
     */
    public function setFirstResult(int $firstResult): static
    {
        $this->page = $firstResult;

        return $this;
    }

    /**
     * The getter function for the property <em>$maxResult</em>.
     *
     * @return int
     */
    public function getMaxResult(): int
    {
        return $this->itemsPerPage;
    }

    /**
     * The setter function for the property <em>$maxResult</em>.
     *
     * @param int $maxResult
     *
     * @return $this Returns the instance of this class.
     */
    public function setMaxResult(int $maxResult): static
    {
        $this->itemsPerPage = $maxResult;

        return $this;
    }
}
