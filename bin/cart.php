    <?php
    class Cart {

    // before
    public static function calculate_total(array $frontEndCart)
    {
        $backendCart = self::get_cart();
        $total = 0;

        foreach ($frontEndCart  as $item) {
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
            $total += $price;
        }



        return $total;
    }

    // refactor with static methods

    public static function calculate_total(array $frontEndCart)
    {
        $backendCart = self::get_cart();
        $total = 0;

        // add up Items that don't have a discount
        $total += Collection::make($frontEndCart)
            ->reject([self::class, 'item_has_discount'])  // return items if they don't have a discount
            ->pluck('price')
            ->sum();


        // add up Items with a Minus Discounts
        $total += Collection::make($frontEndCart)
            ->filter([self::class, 'item_has_discount'])
            ->where('discount.discountType', 'MINUS')
            ->map([self::class, 'item_price_minus_discount_applied'])
            ->sum();


        // add up the percent discounts 
        $total += Collection::make($frontEndCart)
            ->filter([self::class, 'item_has_discount'])
            ->where('discount.discountType', 'PERCENT')
            ->map([self::class, 'item_price_percent_discount_applied'])
            ->sum();

        return $total;
    }

    public static function item_has_discount($item)
    {
        $discount = $item['discount'] ?? [];
        $discountAmount = $discount['discount'] ?? 0;

        return (!empty($discount) && $discountAmount);
    }

    public static function item_price_minus_discount_applied($item)
    {
        $price = $item['price'];
        $discountAmount = (int)$item['discount']['discount'];
        // convert $discountAmount into cents

        $discountAmount =  $discountAmount * 100;

        return (int)round(max($price - $discountAmount, 0));
    }

    public static function item_price_percent_discount_applied($item)
    {
        $price = $item['price'];
        $discountAmount = (int)$item['discount']['discount'];

        // the Discount Amount must be less than or equal to 100 
        $discountAmount = ($discountAmount > 100) ? 100 : $discountAmount;
        if ($discountAmount === 100) {
            return 0;
        }
        // the Discount Amount must be more than 0 ( no negatives )
        $discountAmount = max($discountAmount, 0);
        // convert the  percent off into amount remaining
        // eg 30% off gets converted into 70% off amount
        $discountAmount = abs(100 - $discountAmount);
        // do the percent calculation how I was taught in school
        return (int)round(max(($price / 100) * $discountAmount, 0));
    }




}