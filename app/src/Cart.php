<?php
namespace CodeQuality\src;

use Exception;

class Cart {

    private $items = [];


    public function syncItems(array $items)
    {
        $this->items = $items;

    }

    public function getTotal(): float
    {

        $total = 0;

        foreach ($this->items as $item) {
            
            $price = $item['price'] ?? 0;
            $discount = $item['discount'] ?? [];

            if (!empty($discount)) {

                $discountType = $discount['discountType'] ?? '';
                $discountAmount = $discount['discount'] ?? 0;
                
                $price = $this->calculateDiscount($price, $discountAmount, $discountType);
     
            }
            $total += (float) number_format( $price / 100, 2,'.','');
        }


        return $total;
       
    }

    private function calculateDiscount(int $price, int $discountAmount, string $type) :int
    {
        if(!$discountAmount) {
            return $price;
        }
        if($type == 'PERCENT') {
            return $this->calculatePercentDiscount($price, $discountAmount);

        } elseif  ($type == 'MINUS'){
            return $this->calculateMinusDiscount($price, $discountAmount);
        }
        user_error('Calculate discount requires the discount amount to be a string of "PERCENT" of "MINUS');
    }

    private function calculatePercentDiscount(int $price, int $discountAmount) :int
    {
   
        if($discountAmount < 0) {
            throw new Exception('Discount amount can not be less than 0');
        }
        if( $discountAmount > 100) {
            throw new Exception('Discount amount can not be more than 100');
        }
        
        if($discountAmount === 0) {
            return $price;
        }

        if($discountAmount === 100) {
            return 0;
        }

        // convert the  percent off into amount remaining
        // eg 30% off gets converted into 70% off amount
        $percentOff = abs(100 - $discountAmount);
     
        $price = ($price / 100) * $percentOff;
        $price = max($price,0);
        $price = round($price);
        return $price;

        // https://liamhammett.com/php-function-chaining-with-pipes-47rqArOY
        // return take(($price / 100) * $percentOff )
        //       ->max(PIPED_VALUE,0)
        //       ->round()
        //       ->get();            
    }

    private function calculateMinusDiscount(int $price, int $discountAmount) :int
    {
        if(!$discountAmount) {
            return $price;
        }

        // convert $discountAmount into cents
        $discountAmount = (int) $discountAmount * 100;
        // price minus the discount 
        $price = (int) round(max($price - $discountAmount, 0));

        return $price;
    }
}