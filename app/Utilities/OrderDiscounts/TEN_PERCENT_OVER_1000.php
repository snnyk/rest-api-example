<?php

namespace App\Utilities\OrderDiscounts;

use App\Utilities\DiscountContract;


class TEN_PERCENT_OVER_1000 implements DiscountContract
{

    protected $discountReason = '10_PERCENT_OVER_1000';

    protected $orderDiscount;

    protected $totalDiscount;

    public function __construct($orderDiscount, $totalDiscount)
    {
        $this->orderDiscount = $orderDiscount;

        $this->totalDiscount = $totalDiscount;

    }

    public function rule($order) : bool
    {
        return $order->customer->revenue >= 1000;
    }

    public function handle($order) : array
    {

        if(!$this->rule($order)){
            return [];
        }

        $orderTotal = $order->total - $this->totalDiscount;

        $discountAmount = $this->format((10 / 100) * $orderTotal , 2);

        $subTotal = $this->format($orderTotal - $discountAmount, 2);

        return [
            'discount' => [
                'discountReason' => $this->discountReason,
                'discountAmount' => $discountAmount,
                'subtotal' => $subTotal
            ],
            'total' => $discountAmount
        ];
    }

    protected function format($price)
    {
        return number_format($price , 2);
    }


}
