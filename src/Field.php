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

    /**
     * @var Resource[]
     */
    private $resources = [
        Resources\Wheat::class => 4,
        Resources\Wood::class => 4,
        Resources\Wool::class => 4,
        Resources\Clay::class => 3,
        Resources\Rock::class => 3,
        Resources\Desert::class => 1,
    ];

    private function __construct()
    {
        $this->shuffleResource();

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
                $this->shuffleResource();

                $availableResource = $this->getAvailableResource(
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
            return [
                $this->rows[$rowNum - 1][$rowPosition - 1],
                $this->rows[$rowNum - 1][$rowPosition],
                $this->rows[$rowNum][$rowPosition - 1],
                $this->rows[$rowNum][$rowPosition + 1],
                $this->rows[$rowNum + 1][$rowPosition - 1],
                $this->rows[$rowNum + 1][$rowPosition],
            ];
        } else {
            return [
                $this->rows[$rowNum - 1][$rowPosition],
                $this->rows[$rowNum - 1][$rowPosition + 1],
                $this->rows[$rowNum][$rowPosition - 1],
                $this->rows[$rowNum][$rowPosition + 1],
                $this->rows[$rowNum + 1][$rowPosition],
                $this->rows[$rowNum + 1][$rowPosition + 1],
            ];
        }
    }

    private function getAvailableResource($resources)
    {
        $resources = array_map('get_class', $resources);

        $maxResource = null;

        foreach ($this->resources as $resourceClass => &$count) {
            if (!\in_array($resourceClass, $resources, false) && $this->resources[$resourceClass] > $this->resources[$maxResource]) {
                $maxResource = $resourceClass;
            }
        }

        if ($maxResource !== null) {
            $this->resources[$maxResource]--;
            if ($this->resources[$maxResource] === 0) {
                unset($this->resources[$maxResource]);
            }
            return $maxResource;
        }

        return false;
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
