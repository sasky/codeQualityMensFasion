<?php
namespace CodeQuality\src;

class Cart {

    private $items = [];


    public function syncItems(array $items)
    {
        $this->items = $items;

    }

    public function getTotal():float
    {

        $total = 0;

        foreach ($this->items as $item) {
            
            $price = $item['price'] ?? 0;
            $discount = $item['discount'] ?? [];

            if (!empty($discount)) {

                $discountType = $discount['discountType'] ?? '';
                $discountAmount = $discount['discount'] ?? 0;
                
                if ($discountType === 'PERCENT' && $discountAmount) {
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
                        $price = (int)round(max(($price / 100) * $discountAmount, 0));
                    }
                }
                if ($discountType === 'MINUS' && $discountAmount) {
                    // convert $discountAmount into cents
                    $discountAmount = (int)$discountAmount * 100;
                    $price = (int)round(max($price - $discountAmount, 0));
                }
            }
            $total += (float) number_format( $price / 100, 2,'.','');
        }


        return $total;
       
    }
}