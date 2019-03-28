<?php
namespace Colonizer;

use Colonizer\Resources\Desert;
use Colonizer\Resources\Rock;
use Colonizer\Resources\Wheat;

class Strategy
{
    public const STRATEGIES = [
        'standard' => 'standard',
        'high' => 'high',
        'balance' => 'balance',
        'low' => 'low',
    ];

    private $availableResources = [
        Resources\Wheat::class => 4,
        Resources\Wood::class => 4,
        Resources\Wool::class => 4,
        Resources\Clay::class => 3,
        Resources\Rock::class => 3,
        Resources\Desert::class => 1,
    ];

    private $strategy;
    private $usedResources = [];
    private $startResources = [];
    private $resources = [];
    private $permutationId = 0;

    public function __construct($strategy)
    {
        $this->strategy = $strategy;
        $this->shuffleResource();
        $this->startResources = $this->resources;
    }

    public function guessResource($resources, $row, $rowPosition)
    {
        $availableResources = $this->getAvailableResources($resources);

        if (!empty($availableResources)) {
            $usingResource = $this->strategies()
                [$this->strategy]
                ($availableResources, $resources, $this->usedResources, $row, $rowPosition);

            if ($usingResource === false) {
                return false;
            }

            if (!array_key_exists($usingResource, $this->usedResources)) {
                $this->usedResources[$usingResource] = 1;
            } else {
                $this->usedResources[$usingResource]++;
            }

            $this->resources[$usingResource]--;
            if ($this->resources[$usingResource] === 0) {
                unset($this->resources[$usingResource]);
            }

            return $usingResource;
        }

        return false;
    }

    private function strategies() : array
    {
        return [
            self::STRATEGIES['standard'] => function ($availableResources, $neighbourResources, $usedResources, $row, $rowPosition) {
                if ($row === 2 && $rowPosition === 2) {
                    return Desert::class;
                }

                if (array_key_exists(Desert::class, $availableResources)) {
                    unset($availableResources[Desert::class]);
                }

                $maxResourceClass = null;

                foreach ($availableResources as $resourceClass => $count) {
                    if ($maxResourceClass === null || $count > $availableResources[$maxResourceClass]) {
                        $maxResourceClass = $resourceClass;
                    }
                }

                return $maxResourceClass;
            },
            self::STRATEGIES['high'] => function ($availableResources) {
                $maxResourceClass = null;

                foreach ($availableResources as $resourceClass => $count) {
                    if ($maxResourceClass === null || $count > $availableResources[$maxResourceClass]) {
                        $maxResourceClass = $resourceClass;
                    }
                }

                return $maxResourceClass;
            },
            self::STRATEGIES['balance'] => function ($availableResources, $neighbourResources, $usedResources) {
                $needResourceClass = null;

                while (\count($usedResources) === $this->availableResources ||
                    empty(array_diff_key($availableResources, $usedResources))) {
                    foreach ($usedResources as $resource => &$count) {
                        $count--;
                        if ($count === 0) {
                            unset($usedResources[$resource]);
                        }
                    }
                    unset($count);
                }

                foreach ($availableResources as $resourceClass => $count) {
                    if (!\in_array($resourceClass, $usedResources, false)) {
                        $needResourceClass = $resourceClass;
                    }
                }
                return $needResourceClass;
            },
            self::STRATEGIES['low'] => function ($availableResources, $neighbourResources, $usedResources, $row, $rowPosition) {
                $maxResourceClass = null;
                $neighbourResources = array_map('get_class', $neighbourResources);

                foreach ($availableResources as $resourceClass => $count) {
                    if (($resourceClass === Wheat::class && \in_array(Rock::class, $neighbourResources, true)) ||
                        ($resourceClass === Rock::class && \in_array(Wheat::class, $neighbourResources, true))) {
                        continue;
                    }

                    return $resourceClass;
                }

                return false;
            }
        ];
    }

    public function reset() : void
    {
        $this->resourcePermutation();
    }

    public function getResources()
    {
        return $this->resources;
    }

    private function getAvailableResources($resources) : array
    {
        $availableResources = [];

        $resources = array_map('get_class', $resources);

        foreach ($this->resources as $resourceClass => &$count) {
            if (!\in_array($resourceClass, $resources, false)) {
                $availableResources[$resourceClass] = $count;
            }
        }

        return $availableResources;
    }

    private function resourcePermutation() : void
    {
        $resourceCount = \count($this->startResources);

        if ($this->permutationId === $this->factorial($resourceCount) - 1) {
            throw new \Exception('Out of permutations');
        }

        $this->permutationId++;

        $tmpResources = [];
        $resourcesByNum = array_keys($this->startResources);

        $currentPermutationDigit = $this->permutationId;

        do {
            $resourceNum = $currentPermutationDigit % $resourceCount;
            $resourceClass = $resourcesByNum[$resourceNum];

            $tmpResources[$resourceClass] = $this->startResources[$resourceClass];
            unset($resourcesByNum[$resourceNum]);
            $currentPermutationDigit /= $resourceCount;
            $resourceCount--;
            $resourcesByNum = array_values($resourcesByNum);
        } while ($currentPermutationDigit > 1);

        foreach ($resourcesByNum as $resourceClass) {
            $tmpResources[$resourceClass] = $this->startResources[$resourceClass];
        }

        $this->resources = $tmpResources;
    }

    private function shuffleResource() : void
    {
        $keys = array_keys($this->availableResources);
        shuffle($keys);

        $random = [];
        foreach ($keys as $key) {
            $random[$key] = $this->availableResources[$key];
        }

        $this->resources = $random;
    }

    private function factorial($num) : int
    {
        if ($num < 0) {
            return 0;
        }

        if ($num === 0) {
            return 1;
        }

        return $num*$this->factorial($num-1);
    }
}
