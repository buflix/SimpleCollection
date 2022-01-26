<?php

namespace SimpleCollection;

use InvalidArgumentException;
use OutOfBoundsException;
use SimpleCollection\Service\Pagination\PaginationCollectionInterface;

/**
 * abstract collection
 *
 * @copyright Felix Buchheim
 * @author    Felix Buchheim <hanibal4nothing@gmail.com>
 */
abstract class AbstractCollection implements \Countable, \ArrayAccess, \SeekableIterator, PaginationCollectionInterface, \JsonSerializable
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
     * array with values
     *
     * @var array
     */
    protected array $values = [];

    /**
     * AbstractCollection constructor
     *
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        $this->values = $values;
    }

    /**
     * returns current value
     *
     * @return mixed
     */
    public function current(): mixed
    {
        $key = key($this->values);

        return (true === isset($this->values[$key])) ? $this->values[$key] : null;
    }

    /**
     * improve the pointer
     *
     * @return mixed|false
     */
    #[\ReturnTypeWillChange]
    public function next(): mixed
    {
        return next($this->values);
    }

    /**
     * Like next, but return Not_Set_Flag if there are no next value instead of false
     *
     * @return mixed
     */
    public function scNext(): mixed
    {
        $value = $this->next();

        return (false === $value and false === $this->valid()) ? self::NOT_SET_FLAG : $value;
    }

    /**
     * rewind the pointer for one position
     *
     * @return mixed|false
     */
    public function prev(): mixed
    {
        return prev($this->values);
    }

    /**
     * rewind the pointer for one position
     *
     * @return mixed|false
     */
    public function scPrev(): mixed
    {
        $value = $this->prev();

        return (false === $value and false === $this->valid()) ? self::NOT_SET_FLAG : $value;
    }

    /**
     * return the current key
     *
     * @return string|int|null
     */
    public function key(): string|int|null
    {
        return key($this->values);
    }

    /**
     * check if the current value is set
     *
     * @return bool True if set, otherwise false
     */
    public function valid(): bool
    {
        $key = $this->key();

        return isset($key);
    }

    /**
     * return the first element in collection
     *
     * @return mixed|false
     */
    #[\ReturnTypeWillChange]
    public function rewind(): mixed
    {
        return reset($this->values);
    }

    /**
     * return the last element in collection
     *
     * @return mixed|false
     */
    public function end(): mixed
    {
        return end($this->values);
    }

    /**
     * check if the given offset exists
     *
     * @param string|int $offset Offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->values[$offset]);
    }

    /**
     * return the value with the given offset
     *
     * @param string|int $offset Offset
     *
     * @return mixed
     */
    public function offsetGet($offset): mixed
    {
        return $this->values[$offset];
    }

    /**
     * Return the value with the given offset, if is not set return the default
     *
     * @param string|int $offset
     * @param mixed $default
     *
     * @return mixed
     */
    public function get(string|int $offset, mixed $default = null): mixed
    {
        return (true === isset($this->values[$offset])) ? $this->values[$offset] : $default;
    }

    /**
     * Set all values
     *
     * @param array $values
     *
     * @return $this
     */
    public function set(array $values): static
    {
        $this->values = $values;

        return $this;
    }

    /**
     * set the value by the given offset
     *
     * @param string|int $offset Offset
     * @param mixed $value ProxyServer
     *
     * @return $this
     * @throws InvalidArgumentException
     */
    #[\ReturnTypeWillChange]
    public function offsetSet(mixed $offset, mixed $value): static
    {
        if (false === is_string($offset) and false === is_integer($offset)) {
            throw new InvalidArgumentException('Invalid offset given: ' . gettype($offset));
        }
        $this->values[$offset] = $value;

        return $this;
    }

    /**
     * unset the value by the offset
     *
     * @param string|int $offset Offset
     *
     * @return $this
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset): static
    {
        unset($this->values[$offset]);

        return $this;
    }

    /**
     * count the current entities in the collection
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->values);
    }

    /**
     * seek the pointer to the offset position
     *
     * @param int $offset Seek position
     *
     * @return mixed
     * @throws OutOfBoundsException
     */
    #[\ReturnTypeWillChange]
    public function seek(int $offset): mixed
    {
        $this->rewind();
        $position = 0;

        while ($position < $offset and true === $this->valid()) {
            $this->next();
            $position++;
        }

        if (false === $this->valid()) {
            throw new OutOfBoundsException('Invalid seek position: ' . $offset);
        }

        return $this->current();
    }

    /**
     * Try to seek to given offset
     *
     * @param string|int|null $key
     * @param bool $strictMode
     *
     * @return mixed
     * @throws OutOfBoundsException
     */
    public function seekToKey(string|int|null $key, bool $strictMode = true): mixed
    {
        $this->rewind();
        if ($strictMode === true) {
            while (true === $this->valid() and $key !== $this->key()) {
                $this->next();
            }
        } else {
            while (true === $this->valid() and $key != $this->key()) {
                $this->next();
            }
        }
        if (false === $this->valid()) {
            throw new OutOfBoundsException('Invalid seek position: ' . $key);
        }

        return $this->current();
    }

    /**
     * return if the collection is empty
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return true === empty($this->values);
    }

    /**
     * Return all values
     *
     * @return array
     */
    public function getAll(): array
    {
        return $this->values;
    }

    /**
     * reset the collection keys
     *
     * @return $this
     */
    public function resetKeys(): static
    {
        $this->values = array_values($this->values);

        return $this;
    }

    /**
     * Clear Collection
     *
     * @return $this
     */
    public function clear(): static
    {
        $this->values = [];
        $this->rewind();

        return $this;
    }

    /**
     * Return all used keys
     *
     * @return array
     */
    public function getKeys(): array
    {
        return array_keys($this->values);
    }

    /**
     * Filters the current values and return a new collection
     *
     * @param callable $closure
     *
     * @return AbstractCollection
     */
    public function filter(callable $closure): AbstractCollection
    {
        $className = get_class($this);
        $filteredValues = [];
        foreach ($this->values as $key => $value) {
            if (true === $closure($value, $key)) {
                $filteredValues[$key] = $value;
            }
        }

        return new $className($filteredValues);
    }

    /**
     * Use a function on all values of the collection, and set the result as new values for the key
     *
     * @param callable $closure
     *
     * @return $this
     */
    public function forAll(callable $closure): static
    {
        foreach ($this->values as $key => $value) {
            $this->offsetSet($key, $closure($value, $key));
        }

        return $this;
    }

    /**
     * Slice elements and create a new instance
     *
     * @param string|int|null $startKey
     * @param bool $strict
     * @param int $length
     *
     * @return AbstractCollection
     */
    public function sliceByKey(string|int|null $startKey, bool $strict = true, int $length = PHP_INT_MAX): AbstractCollection
    {
        $slice = [];
        try {
            $this->seekToKey($startKey, $strict);

            if ($length > 0) {
                $slice[$this->key()] = $this->current();
                $length--;
                $this->next();
            }

            while ($length > 0 and true === $this->valid()) {
                $slice[$this->key()] = $this->current();
                $length--;
                $this->next();
            }
        } catch (OutOfBoundsException) {
            //do nothing if key not exists
        }
        $className = get_class($this);

        return new $className($slice);
    }

    /**
     * Check if collection contains an item that fulfill the callback condition
     *
     * @param callable $callable
     *
     * @return bool
     */
    public function contains(callable $callable): bool
    {
        foreach ($this->values as $key => $value) {
            if (true === $callable($value, $key)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return array
     * @see \JsonSerializable
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return $this->getAll();
    }
}
