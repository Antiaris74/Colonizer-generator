<?php
namespace Colonizer;

/**
 * Abstract class for field fill strategies
 * @package Colonizer
 */
abstract class Strategy
{
    protected $availableResources;
    protected $availableNumbers;

    protected $neighbourHexes;

    protected $usedResources = [];
    protected $usedNumber = [];

    public function __construct()
    {

    }

    public function setAvailableResources($availableResources)
    {
        $this->availableResources = $availableResources;
        return $this;
    }

    public function setAvailableNumbers($availableNumbers)
    {
        $this->availableNumbers = $availableNumbers;
        return $this;
    }

    public function setNeighbourHexes($neighbourHexes)
    {
        $this->neighbourHexes = $neighbourHexes;
        return $this;
    }

    public function getHex($row, $rowPosition)
    {
        $resource = $this->checkResources($row, $rowPosition);
        $number = $this->checkNumber($row, $rowPosition);

        if ($resource !== false && $number !== false) {
            $this->useResource($resource);
            $this->useNumber($number);

            return new Hex(new $resource(), $number);
        }

        return false;
    }

    public function reset()
    {

    }

    protected function useResource($resource)
    {
        if (!array_key_exists($resource, $this->usedResources)) {
            $this->usedResources[$resource] = 1;
        } else {
            $this->usedResources[$resource]++;
        }
    }

    protected function useNumber($number)
    {
        if (!array_key_exists($number, $this->usedNumber)) {
            $this->usedNumber[$number] = 1;
        } else {
            $this->usedNumber[$number]++;
        }
    }

    protected function checkDesertPosition($row, $rowPosition)
    {
        return ($row === 2 && $rowPosition === 2);
    }

    abstract protected function checkResources($row, $rowPosition);

    abstract protected function checkNumber($row, $rowPosition);
}
