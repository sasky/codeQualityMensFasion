<?php
namespace CodeQuality\src;

use Exception;
use CodeQuality\src\MinusDiscount;
use CodeQuality\src\PercentDiscount;

class Item {

    protected $itemID = 0;
    protected $price = 0;
    protected $quantity= 1;
    protected $discounts = [];

    public function __construct(array $itemData)
    {
        $this->itemID = $itemData['itemID'] ?? 0;
        $this->price = $itemData['price'] ?? 0;
        $this->quantity = $itemData['quantity'] ?? 0;

        $discountData = $itemData['discount'] ?? [];
        
        if(empty($discountData)){
            return;
        }

        $discountType = $discountData['discountType'] ?? '';
        
        if(!$discountType){
            return;
        }

        if($discountType === 'MINUS') {
            $this->discounts[] = new MinusDiscount($discountData);
        }
        
        if($discountType === 'PERCENT') {
            $this->discounts[] = new PercentDiscount($discountData);
        }
          
    }

    public function getPrice(): int 
    {
        $price = $this->price;

        foreach($this->discounts as $discount) {
            $price = $discount->apply($price);
        }

        return $price;
    }
}
