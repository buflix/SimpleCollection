<?php

namespace SimpleCollection\Service\Pagination;

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
     * @param PaginationCollectionInterface $oCollection
     * @param int                $iPage
     * @param int                $iItemsPerPage
     */
    public function __construct(PaginationCollectionInterface $oCollection, $iPage = 1, $iItemsPerPage = 10)
    {
        $this->collection   = $oCollection;
        $this->page         = $iPage;
        $this->itemsPerPage = $iItemsPerPage;
    }

    /**
     * @see \IteratorAggregate
     *
     * @return AbstractCollection
     * @throws \OutOfBoundsException
     */
    public function getIterator()
    {
        $aResult = array();
        $iStart  = ($this->itemsPerPage * ($this->page - 1));
        if ($iStart < 0) {
            throw new \OutOfBoundsException('Start index cant no be lower then 1');
        }
        try {
            $iRestOfResults                    = ($this->itemsPerPage - 1);
            $mFirstValues                      = $this->collection->seek($iStart);
            $aResult[$this->collection->key()] = $mFirstValues;
            while ($iRestOfResults > 0) {
                $mValue = $this->collection->scNext();
                if ($mValue === AbstractCollection::NOT_SET_FLAG) {
                    throw new \OutOfBoundsException();
                }
                $aResult[$this->collection->key()] = $mValue;
                $iRestOfResults--;
            }

        } catch (\OutOfBoundsException $oException) {
            // do nothing, this exception is not critical
        }
        $sClassName = get_class($this->collection);

        return new $sClassName($aResult);
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