<?php
namespace CodeQuality\src;

use Exception;

class PercentDiscount extends Discount{

    

    public function __construct( array $discountData)
    {
        parent::__construct($discountData);

        if($this->amount < 0) {
            throw new Exception('Discount amount can not be less than 0');
        }
        if( $this->amount > 100) {
            throw new Exception('Discount amount can not be more than 100');
        }

    }

    public function apply(int $price) :int 
    {
        if($this->amount === 0) {
            return $price;
        }

        if($this->amount === 100) {
            return 0;
        }

        // convert the  percent off into amount remaining
        // eg 30% off gets converted into 70% off amount
        $percentOff = abs(100 - $this->amount);
    
        // https://liamhammett.com/php-function-chaining-with-pipes-47rqArOY
        return take(($price / 100) * $percentOff )
              ->max(PIPED_VALUE,0)
              ->round()
              ->get();      
    }
}
