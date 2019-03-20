<?php
namespace Colonizer;

class Row implements \ArrayAccess
{
    private $length;
    private $resources;

    public function __construct(int $length)
    {
        $this->resources = [];
        $this->length = $length;
    }

    public function addResources(Resource $resource) : Row
    {
        $this->resources[] = $resource;

        return $this;
    }

    public function getCurrentFillPosition()
    {
        $position = \count($this->resources);

        if ($this->length !== $position) {
            return $position;
        }

        return false;
    }

    public function getPosition(int $number)
    {
        if (array_key_exists($number, $this->resources)) {
            return $this->resources[$number];
        }

        return false;
    }

    public function getLength() : int
    {
        return $this->length;
    }

    public function getResources() : array
    {
        return $this->resources;
    }

    public function offsetSet($offset, $value) : void
    {
        if (\count($this->resources) === $this->length) {
            if ($offset === null) {
                $this->resources[] = $value;
            } else {
                $this->resources[$offset] = $value;
            }
        }
    }

    public function offsetExists($offset) : bool
    {
        return isset($this->resources[$offset]);
    }

    public function offsetUnset($offset) : void
    {
        unset($this->resources[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->resources[$offset] ?? null;
    }
}
