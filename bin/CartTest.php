<?php
namespace IOD\Tests;

use function GuzzleHttp\json_decode;
use IOD\ECommerce\Model\Cart;
use IOD\ECommerce\Model\ReservedPromoCode;
use SilverStripe\Dev\FunctionalTest;
use SilverStripe\Dev\SapphireTest;

class CartTest extends SapphireTest
{
    public function testCalculationOfTotal()
    {
        // GIVEN we have this cart
        $cart = [
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
        // 86000 + 86500 +  30619 = 203119


        // WHEN we calculate the total 

        $total = Cart::calculate_total($cart);
        // THEN we get this result
        $this->assertEquals(203119, $total);
    }

    public function testCalculationOfTotalWithEdgeCases()
    {
        // GIVEN we have this cart
        $cart = [
            [
                'itemID' => 1,
                'price' => '96000',
                'quantity' => 1,
                'discount' => [
                    'code' => 'ABCDEF',
                    'discount' => 97000, // discount is more than the Item Price 
                    'discountType' => 'MINUS', // 0 
                ],
            ],
            [
                'itemID' => 2,
                'price' => '86500',
            ],
        ];

        // WHEN we calculate the total 

        $total = Cart::calculate_total($cart);
        // THEN price should be the same as the second item
        $this->assertEquals(86500, $total);

        // GIVEN we have this cart
        $cart = [
            [
                'itemID' => 1,
                'price' => '96000',
                'quantity' => 1,
                'discount' => [
                    'code' => 'ABCDEF',
                    'discount' => 100, // 100 percent Off 
                    'discountType' => 'PERCENT', // 0 
                ],
            ],
            [
                'itemID' => 2,
                'price' => '86500',
            ],
        ];

        // WHEN we calculate the total 

        $total = Cart::calculate_total($cart);
        // THEN price should be the same as the second item
        $this->assertEquals(86500, $total);
    }
}
