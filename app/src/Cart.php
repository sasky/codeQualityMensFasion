<?php
namespace CodeQuality\src;

use Exception;
use CodeQuality\src\Item;

class Cart {

    private $items = [];

    public function syncItems(array $items)
    {
        foreach($items as $itemData) {
            $this->items[] =  new Item($itemData);

        }

        return $this;  
    }

    public function getTotal(): float
    {
        $total = 0;

        foreach ($this->items as $item) {

            $total += $item->getPrice();
        }

        return (float) number_format( $total / 100, 2,'.','');
    }
}