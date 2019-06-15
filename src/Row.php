<?php
namespace Colonizer;

/**
 * Row class with array access
 * @package Colonizer
 */
class Row implements \ArrayAccess
{
    /**
     * @var int
     */
    private $length;

    /**
     * @var Hex[]
     */
    private $hexes;

    /**
     * Row constructor.
     * @param int $length
     */
    public function __construct(int $length)
    {
        $this->hexes = [];
        $this->length = $length;
    }

    /**
     * Add hex to row
     * @param Hex $hex
     * @return Row
     */
    public function addHex(Hex $hex) : Row
    {
        $this->hexes[] = $hex;

        return $this;
    }

    /**
     * Get current stack position
     * @return bool|int
     */
    public function getCurrentFillPosition()
    {
        $position = \count($this->hexes);

        if ($this->length !== $position) {
            return $position;
        }

        return false;
    }

    /**
     * Get row hex at position
     * @param int $number
     * @return bool|mixed|Hex
     */
    public function getPosition(int $number)
    {
        if (array_key_exists($number, $this->hexes)) {
            return $this->hexes[$number];
        }

        return false;
    }

    /**
     * Clean row
     * @return $this
     */
    public function clean()
    {
        $this->hexes = [];

        return $this;
    }

    /**
     * Get row length
     * @return int
     */
    public function getLength() : int
    {
        return $this->length;
    }

    /**
     * Get hexes in row
     * @return Hex[]
     */
    public function getHexes() : array
    {
        return $this->hexes;
    }

    /**
     * Array key set
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value) : void
    {
        if (\count($this->hexes) === $this->length) {
            if ($offset === null) {
                $this->hexes[] = $value;
            } else {
                $this->hexes[$offset] = $value;
            }
        }
    }

    /**
     * Array offset check
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset) : bool
    {
        return isset($this->hexes[$offset]);
    }

    /**
     * Array unset
     * @param mixed $offset
     */
    public function offsetUnset($offset) : void
    {
        unset($this->hexes[$offset]);
    }

    /**
     * Array key get
     * @param mixed $offset
     * @return mixed|null|Hex
     */
    public function offsetGet($offset)
    {
        return $this->hexes[$offset] ?? null;
    }
}
