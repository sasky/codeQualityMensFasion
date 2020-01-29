<?php
namespace CodeQuality\src;

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
            return $this->calculatePercentDiscount($price, $discountAmount);
        }
        user_error('Calculate discount requires the discount amount to be a string of "PERCENT" of "MINUS');
    }

    private function calculatePercentDiscount(int $price,int $discountAmount) :int
    {
            // the Discount Amount must be less than or equal to 100 
            $discountAmount = ($discountAmount > 100) ? 100 : $discountAmount;
            // the Discount Amount must be more than 0 ( no negatives )
            $discountAmount = max($discountAmount, 0);
        
            if ($discountAmount === 100) {
                $price = 0;
            } else {
                // convert the  percent off into amount remaining
                // eg 30% off gets converted into 70% off amount
                $discountAmount = abs(100 - $discountAmount);
                // do the percent calculation how I was taught in school
                $price = (int) round (max( ($price / 100) * $discountAmount, 0));
            }
            return $price;
    }

    private function calculateMinusDiscount(int $price, int $discountAmount) :int
    {
        
        // convert $discountAmount into cents
        $discountAmount = (int) $discountAmount * 100;
        // price minus the discount 
        $price = (int) round(max($price - $discountAmount, 0));

        return $price;
    }
}