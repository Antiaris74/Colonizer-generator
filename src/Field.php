<?php
namespace Colonizer;

/**
 * Field class
 * @package Colonizer
 */
class Field
{
    /**
     * Row length
     */
    private const EVERY_ROW_LENGTH = [3,4,5,4,3];

    /**
     * @var Row[]
     */
    private $rows;

    /**
     * @var ResourceManager
     */
    private $strategy;

    /**
     * Field constructor.
     * @param $strategy
     */
    public function __construct($strategy)
    {
        $this->strategy = new ResourceManager($strategy);

        foreach (self::EVERY_ROW_LENGTH as $rowLength) {
            $this->rows[] = new Row($rowLength);
        }
    }

    /**
     * Field fill
     * @return bool
     */
    public function fill() : bool
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
                        $row->addHex($availableResource);
                    } else {
                        $fieldsNotFilled = true;
                    }
                }
            }

            try {
                $this->strategy->reset();
            } catch (\Exception $e) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get field result fill
     * @return array
     */
    public function getFill() : array
    {
        $data = [];

        foreach ($this->rows as $rowNum => $row) {
            $rowResourceChars = [];
            foreach ($row->getHexes() as $hex) {
                $rowResourceChars[] = $hex;
            }
            $data[] = $rowResourceChars;
        }

        return $data;
    }

    /**
     * Get hex neighbours
     * @param $rowNum
     * @param $rowPosition
     * @return array
     */
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

    /**
     * Get row neighbours if hex in corner
     * @param $rowNum
     * @param $rowPosition
     * @return array
     */
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
