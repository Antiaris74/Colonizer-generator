<?php
namespace Colonizer;

abstract class Resource
{
    protected $maximum;
    protected $current;
    protected $chars;

    public function __construct()
    {
        $this->current = $this->maximum;
    }

    public function getMaximum()
    {
        return $this->maximum;
    }

    public function getAvailable()
    {
        return $this->current;
    }

    public function getChars()
    {
        return $this->chars;
    }
}
