<?php

namespace WildWolf\FBR\Response;

/**
 * @property array $fotos
 */
trait ArrayTraits
{
    private $current = 0;

    public function count()
    {
        return count($this->fotos);
    }

    public function current()
    {
        return $this->offsetGet($this->current);
    }

    public function next()
    {
        ++$this->current;
    }

    public function key()
    {
        return $this->valid() ? $this->current : null;
    }

    public function valid()
    {
        return $this->offsetExists($this->current);
    }

    public function rewind()
    {
        $this->current = 0;
    }

    public function offsetGet($offset)
    {
        if ($offset < count($this->fotos)) {
            return $this->fotos[$offset];
        }

        throw new \OutOfBoundsException();
    }

    public function offsetExists($offset)
    {
        return $offset < count($this->fotos);
    }

    public function offsetSet(/** @scrutinizer ignore-unused */$offset, $value)
    {
        throw new \RuntimeException();
    }

    public function offsetUnset(/** @scrutinizer ignore-unused */$offset)
    {
        throw new \RuntimeException();
    }
}
