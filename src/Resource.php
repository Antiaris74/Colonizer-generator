<?php
namespace Colonizer;

/**
 * Abstract class for resource
 * @package Colonizer
 */
abstract class Resource
{
    public static $maximum;
    protected $chars;

    public function __construct()
    {
    }

    public function getChars()
    {
        return $this->chars;
    }
}
