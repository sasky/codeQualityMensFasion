<?php
namespace CodeQuality\src;

use Exception;

class MinusDiscount extends Discount {

    
    public function apply(int $price) :int
    {
        if(!$this->amount) {
            return $price;
        }

        // convert $discountAmount into cents
        $discountAmount = (int) $this->amount * 100;
        // price minus the discount 

        return take($price - $discountAmount)
                ->max(PIPED_VALUE,0)
                ->round()
                ->get();    
    
    }
}
