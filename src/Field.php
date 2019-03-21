<?php
namespace Colonizer;

class Field
{
    private const EVERY_ROW_LENGTH = [3,4,5,4,3];
    private static $instance;

    /**
     * @var Row[]
     */
    private $rows;

    private $strategy;

    private function __construct()
    {
        $this->strategy = new Strategy('high');

        foreach (self::EVERY_ROW_LENGTH as $rowLength) {
            $this->rows[] = new Row($rowLength);
        }
    }

    public static function getInstance() : Field
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function fill() : void
    {
        foreach ($this->rows as $rowNum => $row) {
            for ($i = 0; $i < self::EVERY_ROW_LENGTH[$rowNum]; $i++) {
                $availableResource = $this->strategy->guessResource(
                    $this->getRowNeighbours($rowNum, $row->getCurrentFillPosition())
                );

                if ($availableResource !== false) {
                    $row->addResources(new $availableResource());
                }
            }
        }
    }

    public function printFill() : void
    {
        foreach ($this->rows as $rowNum => $row) {
            $rowResourceChars = [];
            foreach ($row->getResources() as $resource) {
                $rowResourceChars[] = $resource->getChars();
            }
            $rowResourceChars = implode(' ', $rowResourceChars);
            $spaceChars = str_repeat(' ', (9-\strlen($rowResourceChars))/2);
            echo $spaceChars.$rowResourceChars.PHP_EOL;
        }
    }

    private function getRowNeighbours($rowNum, $rowPosition) : array
    {
        //ToDo: Various optimizations for row searching like first or last row element
        if ($rowNum <= 2) {
            if ($rowPosition === 0 || $rowPosition === self::EVERY_ROW_LENGTH[$rowNum]) {
                $rows = $this->getCornerPosition($rowNum, $rowPosition);
            } elseif ($rowNum === 1 || $rowNum === 2) {
                $rows = [
                    $this->rows[$rowNum - 1][$rowPosition - 1],
                    $this->rows[$rowNum - 1][$rowPosition],
                    $this->rows[$rowNum][$rowPosition - 1],
                    $this->rows[$rowNum][$rowPosition + 1],
                    $this->rows[$rowNum + 1][$rowPosition - 1],
                    $this->rows[$rowNum + 1][$rowPosition],
                ];
            } else {
                $rows = [
                    $this->rows[$rowNum][$rowPosition - 1],
                    $this->rows[$rowNum][$rowPosition + 1],
                    $this->rows[$rowNum + 1][$rowPosition - 1],
                    $this->rows[$rowNum + 1][$rowPosition],
                ];
            }
        } elseif ($rowNum === 3) {
            $rows = [
                $this->rows[$rowNum - 1][$rowPosition],
                $this->rows[$rowNum - 1][$rowPosition + 1],
                $this->rows[$rowNum][$rowPosition - 1],
                $this->rows[$rowNum][$rowPosition + 1],
                $this->rows[$rowNum + 1][$rowPosition],
                $this->rows[$rowNum + 1][$rowPosition + 1],
            ];
        } else {
            $rows = [
                $this->rows[$rowNum - 1][$rowPosition],
                $this->rows[$rowNum - 1][$rowPosition + 1],
                $this->rows[$rowNum][$rowPosition - 1],
                $this->rows[$rowNum][$rowPosition + 1],
            ];
        }

        return array_filter($rows);
    }

    private function getCornerPosition($rowNum, $rowPosition) : array
    {
        $rows = [];

        if ($rowPosition === 0) {
            $rows[] = $this->rows[$rowNum][$rowPosition+1];

            if ($rowNum === 0) {
                $rows[] = $this->rows[$rowNum+1][$rowPosition];
                $rows[] = $this->rows[$rowNum+1][$rowPosition+1];
            } elseif ($rowNum === 2) {
                $rows[] = $this->rows[$rowNum-1][$rowPosition];
                $rows[] = $this->rows[$rowNum+1][$rowPosition];
            } elseif ($rowNum === 4) {
                $rows[] = $this->rows[$rowNum-1][$rowPosition];
                $rows[] = $this->rows[$rowNum-1][$rowPosition+1];
            } else {
                $rows[] = $this->rows[$rowNum-1][$rowPosition];
                $rows[] = $this->rows[$rowNum-1][$rowPosition+1];
                $rows[] = $this->rows[$rowNum+1][$rowPosition];
                $rows[] = $this->rows[$rowNum+1][$rowPosition+1];
            }
        } else {
            $rows[] = $this->rows[$rowNum][$rowPosition-1];

            if ($rowNum === 0) {
                $rows[] = $this->rows[$rowNum+1][$rowPosition];
                $rows[] = $this->rows[$rowNum+1][$rowPosition+1];
            } elseif ($rowNum === 2) {
                $rows[] = $this->rows[$rowNum-1][$rowPosition-1];
                $rows[] = $this->rows[$rowNum+1][$rowPosition-1];
            } elseif ($rowNum === 4) {
                $rows[] = $this->rows[$rowNum-1][$rowPosition];
                $rows[] = $this->rows[$rowNum-1][$rowPosition+1];
            } else {
                $rows[] = $this->rows[$rowNum-1][$rowPosition];
                $rows[] = $this->rows[$rowNum-1][$rowPosition+1];
                $rows[] = $this->rows[$rowNum+1][$rowPosition];
                $rows[] = $this->rows[$rowNum+1][$rowPosition+1];
            }
        }

        return $rows;
    }
}
