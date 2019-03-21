<?php
namespace Colonizer;

class Strategy
{
    public const STRATEGIES = [
        'high' => 'high',
        'balance' => 'balance',
        'low' => 'low',
    ];

    private $startAvailableResources = [
        Resources\Wheat::class => 4,
        Resources\Wood::class => 4,
        Resources\Wool::class => 4,
        Resources\Clay::class => 3,
        Resources\Rock::class => 3,
        Resources\Desert::class => 1,
    ];
    private $strategy;
    private $usedResources = [];
    private $resources = [];

    public function __construct($strategy)
    {
        $this->strategy = $strategy;
        $this->reset();
    }

    public function guessResource($resources)
    {
        $availableResources = $this->getAvailableResources($resources);

        if (!empty($availableResources)) {
            $usingResource = $this->strategies()
                [$this->strategy]
                ($availableResources, $resources, $this->usedResources);

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
            self::STRATEGIES['high'] => function ($availableResources, $neighbourResources, $usedResources) {
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

                while (\count($usedResources) === $this->startAvailableResources ||
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
                print_r($availableResources);
                return $needResourceClass;
            },
            self::STRATEGIES['low'] => function ($availableResources, $neighbourResources, $usedResources) {
                $maxResourceClass = null;

                foreach ($availableResources as $resourceClass => $count) {
                    if ($maxResourceClass === null || $count < $availableResources[$maxResourceClass]) {
                        $maxResourceClass = $resourceClass;
                    }
                }

                return $maxResourceClass;
            }
        ];
    }

    private function reset() : void
    {
        $this->resources = $this->startAvailableResources;
        $this->shuffleResource();
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

    private function shuffleResource() : void
    {
        $keys = array_keys($this->resources);
        shuffle($keys);

        $random = [];
        foreach ($keys as $key) {
            $random[$key] = $this->resources[$key];
        }

        $this->resources = $random;
    }
}
