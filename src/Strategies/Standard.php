<?php
namespace Colonizer\Strategies;

use Colonizer\Resources\Desert;
use Colonizer\Strategy;

class Standard extends Strategy
{
    protected function checkResources($row, $rowPosition)
    {
        if ($row === 2 && $rowPosition === 2) {
            return Desert::class;
        } else {
            unset($this->availableResources[Desert::class]);
        }

        $maxResourceClass = false;

        foreach ($this->availableResources as $resourceClass => $count) {
            if ($maxResourceClass === false || $count > $this->availableResources[$maxResourceClass]) {
                $maxResourceClass = $resourceClass;
            }
        }

        return $maxResourceClass;
    }

    protected function checkNumber($row, $rowPosition)
    {
        if ($this->checkDesertPosition($row, $rowPosition)) {
            return '';
        } else {
            unset($this->availableNumbers['']);
        }

        if (!empty($this->availableNumbers)) {
            return array_rand($this->availableNumbers);
        }

        return false;
    }
}
