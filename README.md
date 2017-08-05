# SimpleCollection

### *Package for handling abstract collection of entities.*

## API

### SimpleCollection\AbstractCollection (**abstract**)

Contains a general definition of an collection.

Can be initialized with a collection as array on construct.

#### $collection->currentValue()
 
Method to retrieve the value of the current pointer position. Will return `null` when value does not exist.

#### $collection->next()

Method to advance the pointer by one.

#### $collection->scNext()

Method to advance the pointer by one. If no next value available method will return the `self::NOT_SET_FLAG`.

#### $collection->prev()

Method to decrease pointer by one.
 
#### $collection->scPrev()
 
Method to decrease pointer by one. If no previous value available  method will return the `self::NOT_SET_FLAG`. 
 
#### $collection->key()

Method to retrieve the current pointer position.

#### $collection->valid()

Method to check if the value at the current pointer position is set.

#### $collection->rewind()

Method to reset the pointer to the first element.

#### $collection->end()

Method to set the pointer to the last element.

#### $collection->offsetExists($offset)

Method to check if offset exists on value collection.

##### Arguments

| Argument | Type |
| --- | --- |
| $offset | `string|int` |

#### $collection->offsetGet($offset)

Method to retrieve the value set at provided offset.

##### Arguments

| Argument | Type |
| --- | --- |
| $offset | `string|int` |

#### $collection->get($offset, $default = null)

Method to retrieve value set at provided offset. In case it does not `$default` will be returned.

##### Arguments

| Argument | Type |
| --- | --- |
| $offset | `string|int` |
| $default | `mixed` |

#### $collection->update(array $values)

Method to update the collection values completely. Method does create so immutable, to assure data integrity.  

##### Arguments

| Argument | Type |
| --- | --- |
| $values | `array` |

#### $collection->offsetSet($offset, $value)

Method to set a new value to the current collection.  

##### Arguments

| Argument | Type |
| --- | --- |
| $offset | `string|int` |
| $value | `mixed` |

#### $collection->offsetUnset($offset)

Method to unset a value at position.  

##### Arguments

| Argument | Type |
| --- | --- |
| $offset | `string|int` |

#### $collection->count()

Method to retrieve the count of elements stored in the collection.
  
#### $collection->seek($offset)

Method to move the pointer to the given offset and return the value set.

##### Arguments

| Argument | Type |
| --- | --- |
| $offset | `string|int` |

#### $collection->seekToKey($key, $strict = true)

Method to move the pointer to the given key and return the value on this position. If `$strict` is true the check if key is reached will be type safe.

##### Arguments

| Argument | Type |
| --- | --- |
| $key | `string|int` |
| $strict | `bool` |

#### $collection->seekToKey($key, $strict = true)

Method to move the pointer to the given key and returns it. If `$strict` is true the check if key is reached will be type safe.

##### Arguments

| Argument | Type |
| --- | --- |
| $key | `string|int` |
| $strict | `bool` |

#### $collection->isEmpty()

Method to check if the current collection is not empty.

#### $collection->getAll()

Method to retrieve the whole collection as array.

#### $collection->resetKeys()

Method to re-apply the numeric index to the collection.

#### $collection->clear()

Method to set the collection empty and rest the pointer to the first element.

#### $collection->clear()

Method to set the collection empty and rest the pointer to the first element.

#### $collection->filter($closure)

Method to filter collection and return a collection which conform to the filter process.
Method does create an immutable state to assure data integrity.

##### Arguments

| Argument | Type |
| --- | --- |
| $closure | `callable` |

#### $collection->forAll($closure)

Method to apply a method to all values in a collection.
Method does create an immutable state to assure data integrity.

##### Arguments

| Argument | Type |
| --- | --- |
| $closure | `callable` |

#### $collection->sliceByKey($startKey, $strict = true, $length = PHP_INT_MAX)

Method to create a new list starting from `$startKey`. 

### SimpleCollection\ArrayCollection

Contains a specific definition of an non associative array collection.

#### $collection->add($value)

Method to add a new value to the collection.

##### Arguments

| Argument | Type |
| --- | --- |
| $value | `mixed` |

#### $collection->update($values)

Method to reset the value collection.
Method does create an immutable state to assure data integrity.

##### Arguments

| Argument | Type |
| --- | --- |
| $values | `array` |