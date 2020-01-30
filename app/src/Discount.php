<?php
namespace CodeQuality\src;

use Exception;

class Discount {

    protected $code = '';
    protected $amount = 0;

    public function __construct( array $discountData)
    {
        $this->code = $discountData['code'] ?? '';
        $this->amount = $discountData['discount'] ?? 0;
    }

    public function calculate(int $price) :int 
    {
        user_error('must be implemented by the child class');
        return 0;
    }
}
