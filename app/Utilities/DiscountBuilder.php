<?php

namespace App\Utilities;

use App\Models\Order;

class DiscountBuilder
{
    //order id
    protected $order;

    //discount object folder
    protected $namespace = 'App\Utilities\OrderDiscounts';

    //discount classes
    protected $discounts = [
        'TEN_PERCENT_OVER_1000',
        'BUY_5_GET_1',
        'GET_20_PERCENT'
    ];

    //discount data
    protected $orderDiscounts = [];

    //total discount
    protected $totalDiscount = 0;

    public function __construct($id)
    {
        $this->order = Order::find($id);
    }

    public function apply()
    {

        foreach ($this->discounts as $discount) {

            $class = $this->namespace . "\\{$discount}";

            if (! class_exists($class)) {
                continue;
            }

            $discountData = (new $class($this->orderDiscounts, $this->totalDiscount))->handle($this->order);

            if(empty($discountData)){
                continue;
            }

            array_push($this->orderDiscounts, $discountData['discount']);

            $this->totalDiscount += $discountData['total'];
        }


    }

    public function orderDiscounts()
    {
        return $this->orderDiscounts;
    }

    public function totalDiscount()
    {
        return $this->totalDiscount;
    }

    public function discountedTotal()
    {
        return $this->order->total - $this->totalDiscount;
    }
}
