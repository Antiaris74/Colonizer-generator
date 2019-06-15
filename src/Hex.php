<?php
namespace Colonizer;

/**
 * Hex class
 * @package Colonizer
 */
class Hex
{
    /**
     * @var Resource
     */
    private $resource;
    /**
     * @var string
     */
    private $number;

    /**
     * Hex constructor.
     * @param Resource $resource
     * @param string $number
     */
    public function __construct(Resource $resource, string $number)
    {
        $this->resource = $resource;
        $this->number = $number;
    }

    /**
     * Get hex resource
     * @return Resource
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Get hex number
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }
}
