<?php
namespace Colonizer;

/**
 * Class for array permutation with key saving
 * @package Colonizer
 */
class Permutation
{
    /**
     * @var int
     */
    private $currentPermutation = 0;
    /**
     * @var int
     */
    private $permutationMaxNumber;
    /**
     * @var int
     */
    private $arraySize;
    /**
     * @var array
     */
    private $initialArray;
    /**
     * @var array
     */
    private $resultArray;

    /**
     * Permutation constructor.
     * @param array $startArray
     */
    public function __construct(array $startArray)
    {
        $this->arraySize = \count($startArray);
        $this->permutationMaxNumber = $this->factorial($this->arraySize);
        $this->initialArray = $startArray;
    }

    /**
     * Set current permutation number
     * @param $permutationNumber
     * @return $this
     */
    public function setPermutationNumber($permutationNumber)
    {
        $this->currentPermutation = $permutationNumber;

        return $this;
    }

    /**
     * Get next permutation
     * @return $this
     */
    public function next()
    {
        $this->currentPermutation++;

        return $this;
    }

    /**
     * Get permutation result
     * @return array
     */
    public function getResult()
    {
        $this->getPermutation($this->currentPermutation);

        return $this->resultArray;
    }

    /**
     * Get current permutation number
     * @return int
     */
    public function getPermutationNumber()
    {
        return $this->currentPermutation;
    }

    /**
     * Get permutation by number
     * @param $permutationNumber
     * @return $this
     */
    private function getPermutation($permutationNumber)
    {
        $this->checkPermutation($permutationNumber);

        $this->resultArray = [];
        $resourceCount = $this->arraySize;
        $resourcesByNum = array_keys($this->initialArray);

        $currentPermutationDigit = $permutationNumber;

        do {
            $resourceNum = $currentPermutationDigit % $resourceCount;
            $resourceClass = $resourcesByNum[$resourceNum];

            $this->resultArray[$resourceClass] = $this->initialArray[$resourceClass];
            unset($resourcesByNum[$resourceNum]);
            $currentPermutationDigit /= $resourceCount;
            $resourceCount--;
            $resourcesByNum = array_values($resourcesByNum);
        } while ($currentPermutationDigit > 1);

        foreach ($resourcesByNum as $resourceClass) {
            $this->resultArray[$resourceClass] = $this->initialArray[$resourceClass];
        }

        return $this;
    }

    /**
     * Check permutation number
     * @param $permutationNumber
     * @throws \Exception
     */
    private function checkPermutation($permutationNumber)
    {
        if ($permutationNumber >= $this->permutationMaxNumber) {
            throw new \Exception('Out of permutations');
        }
    }

    /**
     * @param $num
     * @return int
     */
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