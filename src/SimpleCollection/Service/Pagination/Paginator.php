<?php

namespace SimpleCollection\Service\Pagination;

use SimpleCollection\Base\ScCollection;

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
     * @var PaginationCollectionInterface
     */
    protected $collection;

    /**
     * Index of first result
     *
     * @var int
     */
    protected $page;

    /**
     * Amount of results
     *
     * @var int
     */
    protected $itemsPerPage;

    /**
     * Paginator constructor.
     *
     * @param PaginationCollectionInterface $collection
     * @param int                           $page
     * @param int                           $itemsPerPage
     */
    public function __construct(PaginationCollectionInterface $collection, $page = 1, $itemsPerPage = 10)
    {
        $this->collection   = $collection;
        $this->page         = $page;
        $this->itemsPerPage = $itemsPerPage;
    }

    /**
     * @see \IteratorAggregate
     *
     * @return ScCollection
     * @throws \OutOfBoundsException
     */
    public function getIterator()
    {
        $result        = array();
        $startPosition = $this->fetchStartPosition();
        try {
            $restOfResults                    = ($this->itemsPerPage - 1);
            $firstValue                       = $this->collection->seek($startPosition);
            $result[$this->collection->key()] = $firstValue;
            while ($restOfResults > 0) {
                $value = $this->collection->scNext();
                if ($value === ScCollection::NOT_SET_FLAG) {
                    throw new \OutOfBoundsException();
                }
                $result[$this->collection->key()] = $value;
                $restOfResults--;
            }

        }
        catch (\OutOfBoundsException $exception) {
            // do nothing, this exception is not critical
        }
        $className = get_class($this->collection);

        return new $className($result);
    }

    /**
     * Fetch startPosition to seek
     *
     * @return int
     * @throws \OutOfBoundsException
     */
    protected function fetchStartPosition()
    {
        $start = ($this->itemsPerPage * ($this->page - 1));
        if ($start < 0) {
            throw new \OutOfBoundsException('Start index cant no be lower then 1');
        }

        return $start;
    }

    /**
     * @see \Countable
     *
     * @return int
     */
    public function count()
    {
        return count($this->getIterator());
    }

    /**
     * @return int
     */
    public function fetchPageCount()
    {
        return (int) ceil($this->collection->count() / $this->itemsPerPage);
    }

    /**
     * The getter function for the property <em>$firstResult</em>.
     *
     * @return int
     */
    public function getFirstResult()
    {
        return $this->page;
    }

    /**
     * The setter function for the property <em>$firstResult</em>.
     *
     * @param  int $firstResult
     *
     * @return $this Returns the instance of this class.
     */
    public function setFirstResult($firstResult)
    {
        $this->page = $firstResult;

        return $this;
    }

    /**
     * The getter function for the property <em>$maxResult</em>.
     *
     * @return int
     */
    public function getMaxResult()
    {
        return $this->itemsPerPage;
    }

    /**
     * The setter function for the property <em>$maxResult</em>.
     *
     * @param  int $maxResult
     *
     * @return $this Returns the instance of this class.
     */
    public function setMaxResult($maxResult)
    {
        $this->itemsPerPage = $maxResult;

        return $this;
    }
}