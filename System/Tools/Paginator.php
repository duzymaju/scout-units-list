<?php

namespace ScoutUnitsList\System\Tools;

use ScoutUnitsList\System\Tools\ArrayObject;

/**
 * System tools paginator
 */
class Paginator extends ArrayObject
{
    /** @var string */
    const ORDER_ASC = 'asc';

    /** @var string */
    const ORDER_DESC = 'desc';

    /** @var int */
    public $packNo;

    /** @var int|null */
    public $packSize;

    /** @var int|null */
    public $totalSize;

    /** @var array */
    public $order;

    /**
     * Constructor
     *
     * @param array    $items    items
     * @param int      $packNo   pack no
     * @param int|null $packSize pack size
     * @param array    $order    order
     */
    public function __construct(array $items, $packNo = 1, $packSize = null, array $order = [])
    {
        parent::__construct($items);

        $this->packNo = max(1, (int) $packNo);
        $this->packSize = isset($packSize) ? abs((int) $packSize) : null;
        $this->order = $order;
    }

    /**
     * Get
     * 
     * @param string $name name
     *
     * @return mixed
     */
    public function __get($name)
    {
        switch ($name) {
            case 'packsCount':
                return isset($this->totalSize) && isset($this->packSize) && $this->packSize > 0 ?
                    ceil($this->totalSize / $this->packSize) : (count($this) > 0 ? 1 : 0);

            case 'count':
                return count($this);
        }
    }
}
