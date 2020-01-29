<?php
namespace MensFashion\Tests;


use CodeQuality\src\Cart;
use SilverStripe\Dev\SapphireTest;

class CartTest extends SapphireTest
{
    public function test_calculation_of_total()
    {
        // GIVEN we have this cart
        $items = [
            [
                'itemID' => 1,
                'price' => '96000',
                'quantity' => 1,
                'discount' => [
                    'code' => 'ABCDEF', // 96000- 100 = 86000
                    'discount' => 100,
                    'discountType' => 'MINUS',
                ],
            ],
            [
                'itemID' => 2,
                'price' => '86500',
            ],
            [
                'itemID' => 3,
                'price' => '45700', // 33% off 45700 is  30619
                'discount' => [
                    'code' => 'ABCDEF',
                    'discount' => 33,
                    'discountType' => 'PERCENT',
                ],
            ],
        ];
        // The Total will be 86000 + 86500 +  30619 = 203119 ( in cents)

        // WHEN  we add the items into the cart 
        $cart = new Cart();
        $cart->syncItems($items);

        // THEN we get this total $2031.19

        $total = $cart->getTotal(); 
        $this->assertEquals(2031.19, $total);
    }
}