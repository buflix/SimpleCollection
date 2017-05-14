<?php

namespace SimpleCollection\Base;

use SimpleCollection\Service\Pagination\PaginationCollectionInterface;

/**
 * Abstract Collection with sc methods
 *
 * @author    Felix Buchheim <hanibal4nothing@gmail.com>
 * @copyright Felix Buchheim
 */
class ScCollection extends Collection implements PaginationCollectionInterface
{
    /**
     * Flag if the value no existing
     *
     * To be able to distinguish existing values such as "null" or "false" from the information that the requested
     * value is not present
     *
     * @var string
     */
    const NOT_SET_FLAG = '<|__NOT_SET__|>';

    /**
     * Like next, but return Not_Set_Flag if there are no next value instead of false
     *
     * @return mixed
     */
    public function scNext()
    {
        $value = $this->next();

        return (false === $value && !$this->valid()) ? self::NOT_SET_FLAG : $value;
    }

    /**
     * rewind the pointer for one position
     *
     * @return mixed|false
     */
    public function scPrev()
    {
        $value = $this->prev();

        return (false === $value && !$this->valid()) ? self::NOT_SET_FLAG : $value;
    }
}