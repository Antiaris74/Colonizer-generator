<?php
namespace Colonizer;

class Field
{
    private const EVERY_ROW_LENGTH = [3,4,5,4,3];

    /**
     * @var Row[]
     */
    private $rows;

    private $strategy;

    public function __construct($strategy)
    {
        $this->strategy = new Strategy($strategy);

        foreach (self::EVERY_ROW_LENGTH as $rowLength) {
            $this->rows[] = new Row($rowLength);
        }
    }

    public function fill() : void
    {
        $fieldsNotFilled = true;

        while ($fieldsNotFilled) {
            $fieldsNotFilled = false;
            foreach ($this->rows as $rowNum => $row) {
                $row->clean();
                for ($i = 0; $i < self::EVERY_ROW_LENGTH[$rowNum]; $i++) {
                    $availableResource = $this->strategy->guessResource(
                        $this->getRowNeighbours($rowNum, $row->getCurrentFillPosition()),
                        $rowNum,
                        $row->getCurrentFillPosition()
                    );

                    if ($availableResource !== false) {
                        $row->addResources(new $availableResource());
                    } else {
                        $fieldsNotFilled = true;
                    }
                }
            }
            $this->strategy->reset();
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
        $rows = [
            $this->rows[$rowNum][$rowPosition - 1],
            $this->rows[$rowNum][$rowPosition + 1]
        ];

        if ($rowNum <= 2) {
            if ($rowPosition === 0 || $rowPosition === self::EVERY_ROW_LENGTH[$rowNum]) {
                $rows = $this->getCornerPosition($rowNum, $rowPosition);
            } else {
                if ($rowNum === 1 || $rowNum === 2) {
                    $rows[] = $this->rows[$rowNum - 1][$rowPosition - 1];
                    $rows[] = $this->rows[$rowNum - 1][$rowPosition];
                }
                $rows[] = $this->rows[$rowNum + 1][$rowPosition - 1];
                $rows[] = $this->rows[$rowNum + 1][$rowPosition];
            }
        } else {
            $rows[] = $this->rows[$rowNum - 1][$rowPosition];
            $rows[] = $this->rows[$rowNum - 1][$rowPosition + 1];
            if ($rowNum === 3) {
                $rows[] = $this->rows[$rowNum + 1][$rowPosition];
                $rows[] = $this->rows[$rowNum + 1][$rowPosition + 1];
            }
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
