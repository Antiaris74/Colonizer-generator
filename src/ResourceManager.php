<?php
namespace Colonizer;

use Colonizer\Resources\Desert;
use Colonizer\Resources\Rock;
use Colonizer\Resources\Wheat;

/**
 * Resource manager class
 * @package Colonizer
 */
class ResourceManager
{
    public const STRATEGIES = [
        'standard' => Strategies\Standard::class,
        'balance' => Strategies\Balance::class,
    ];

    private $availableResources = [];

    private $availableNumbers = [
        '' => 1,
        '2' => 1,
        '3' => 2,
        '4' => 2,
        '5' => 2,
        '6' => 2,
        '8' => 2,
        '9' => 2,
        '10' => 2,
        '11' => 2,
        '12' => 1
    ];

    /**
     * @var Strategy $strategy
     */
    private $strategy;

    /**
     * @var Permutation
     */
    private $resourcePermutation;

    /**
     * @var array
     */
    private $resources = [];

    /**
     * @var array
     */
    private $numbers = [];

    /**
     * ResourceManager constructor.
     * @param $strategy
     */
    public function __construct($strategy)
    {
        $this->initResources();

        $strategyClass = self::STRATEGIES[$strategy];
        $this->strategy = new $strategyClass();

        $this->resources = $this->shuffleArray($this->availableResources);
        $this->numbers = $this->shuffleArray($this->availableNumbers);

        $this->resourcePermutation = new Permutation($this->resources);
    }

    public function guessResource($neighbourHexes, $row, $rowPosition)
    {
        $availableResources = $this->getAvailableResources($neighbourHexes);
        $availableNumbers = $this->getAvailableNumbers($neighbourHexes);

        if (!empty($availableResources) && !empty($availableNumbers)) {
            $this->strategy
                ->setNeighbourHexes($neighbourHexes)
                ->setAvailableResources($availableResources)
                ->setAvailableNumbers($availableNumbers);

            $hex = $this->strategy->getHex($row, $rowPosition);

            if ($hex === false) {
                return false;
            }

            $this->useResource(get_class($hex->getResource()));
            $this->useNumber($hex->getNumber());

            return $hex;
        }

        return false;
    }

    public function reset() : void
    {
        $this->strategy->reset();
        $this->resources = $this->resourcePermutation->next()->getResult();
        $this->numbers = $this->availableNumbers;
    }

    public function getResources()
    {
        return $this->resources;
    }

    private function initResources()
    {
        $resources = [
            Resources\Wheat::class,
            Resources\Wood::class,
            Resources\Wool::class,
            Resources\Clay::class,
            Resources\Rock::class,
            Resources\Desert::class
        ];

        foreach ($resources as $resourceClass) {
            $this->availableResources[$resourceClass] = $resourceClass::$maximum;
        }
    }

    private function getAvailableResources($neighbourHexes) : array
    {
        $availableResources = [];

        $neighbourResources = $this->getResourcesFromHexes($neighbourHexes);

        foreach ($this->resources as $resourceClass => &$count) {
            if (!\in_array($resourceClass, $neighbourResources, false)) {
                $availableResources[$resourceClass] = $count;
            }
        }

        return $availableResources;
    }

    private function getAvailableNumbers($neighbourHexes) : array
    {
        $availableNumbers = [];

        $neighbourNumbers = $this->getNumbersFromHexes($neighbourHexes);

        foreach ($this->numbers as $number => &$count) {
            if (!\in_array($number, $neighbourNumbers, false)) {
                $availableNumbers[$number] = $count;
            }
        }

        return $availableNumbers;
    }

    /**
     * @param Hex[] $hexes
     * @return array
     */
    private function getResourcesFromHexes($hexes)
    {
        $resources = [];

        foreach ($hexes as $hex) {
            $resources[] = get_class($hex->getResource());
        }

        return $resources;
    }

    /**
     * @param Hex[] $hexes
     * @return array
     */
    private function getNumbersFromHexes($hexes)
    {
        $numbers = [];

        foreach ($hexes as $hex) {
            $numbers[] = $hex->getNumber();
        }

        return $numbers;
    }

    /**
     * Resource count decrement
     * @param $resource
     * @return $this
     */
    private function useResource($resource)
    {
        $this->resources[$resource]--;
        if ($this->resources[$resource] === 0) {
            unset($this->resources[$resource]);
        }

        return $this;
    }

    /**
     * Number count decrease
     * @param $number
     * @return $this
     */
    private function useNumber($number)
    {
        $this->numbers[$number]--;
        if ($this->numbers[$number] === 0) {
            unset($this->numbers[$number]);
        }

        return $this;
    }

    /**
     * Initial array shuffle
     * @param $array
     * @return array
     */
    private function shuffleArray($array)
    {
        $keys = array_keys($array);
        shuffle($keys);

        $random = [];
        foreach ($keys as $key) {
            $random[$key] = $array[$key];
        }

        return $random;
    }
}
