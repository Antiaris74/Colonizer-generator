<?php
namespace Colonizer\Strategies;

use Colonizer\Resources\Desert;
use Colonizer\Strategy;

class Balance extends Strategy
{
    private $cornerResource = [Desert::class];

    public function reset()
    {
        parent::reset();
        $this->cornerResource = [Desert::class];
    }

    protected function checkResources($row, $rowPosition)
    {
        $maxResourceClass = false;

        if ($this->checkCornerPosition($row, $rowPosition)) {
            foreach ($this->availableResources as $resourceClass => $count) {
                if (($maxResourceClass === false || $count > $this->availableResources[$maxResourceClass]) && !in_array($resourceClass, $this->cornerResource)) {
                    $maxResourceClass = $resourceClass;
                    $this->cornerResource[] = $resourceClass;
                }
            }
        } else {
            if ($row === 2 && $rowPosition === 2) {
                return Desert::class;
            } else {
                unset($this->availableResources[Desert::class]);
            }

            foreach ($this->availableResources as $resourceClass => $count) {
                if ($maxResourceClass === false || $count > $this->availableResources[$maxResourceClass]) {
                    $maxResourceClass = $resourceClass;
                }
            }
        }

        return $maxResourceClass;
    }

    protected function checkNumber($row, $rowPosition)
    {
        $reservedCorner = [
            8, 6
        ];

        $reservedTopAndBottom = [
            2, 12
        ];


        if ($this->checkCornerPosition($row, $rowPosition)) {
            $availableReservedNumbers = array_values(array_intersect($reservedCorner, array_keys($this->availableNumbers)));
            return $availableReservedNumbers[mt_rand(0, count($availableReservedNumbers) - 1)];
        } elseif ($this->checkTopAndBottomPosition($row, $rowPosition)) {
            $availableReservedNumbers = array_values(array_intersect($reservedTopAndBottom, array_keys($this->availableNumbers)));
            return $availableReservedNumbers[mt_rand(0, count($availableReservedNumbers) - 1)];
        } else {
            unset($this->availableNumbers['8'], $this->availableNumbers['6'], $this->availableNumbers['2'], $this->availableNumbers['12']);
        }

        if ($this->checkDesertPosition($row, $rowPosition)) {
            return '';
        } else {
            unset($this->availableNumbers['']);
        }

        if (!empty($this->availableNumbers)) {
            return array_rand($this->availableNumbers);
        } else {
            return false;
        }
    }

    private function checkCornerPosition($row, $rowPosition)
    {
        return ($rowPosition === 0 || $rowPosition === 2) && ($row === 0 || $row === 4);
    }

    private function checkTopAndBottomPosition($row, $rowPosition)
    {
        return ($rowPosition === 1) && ($row === 0 || $row === 4);
    }
}
