<?php

namespace App\Utilities\OrderDiscounts;

use App\Models\Product;
use App\Utilities\DiscountContract;


class GET_20_PERCENT implements DiscountContract
{

    protected $discountReason = 'GET_20_PERCENT';

    protected $orderDiscount;

    protected $totalDiscount;


    public function __construct($orderDiscount, $totalDiscount)
    {
        $this->orderDiscount = $orderDiscount;

        $this->totalDiscount = $totalDiscount;

    }

    public function rule($order) : bool
    {

        $will_be_discount = $this->will_be_discount($order->items);

        return !empty($will_be_discount);
    }

    public function handle($order) : array
    {

        if(!$this->rule($order)){
            return [];
        }

        $orderTotal = $order->total - $this->totalDiscount;

        $discountAmount = $this->calc_discount($order->items);

        $subTotal = $this->format($orderTotal - $discountAmount, 2);

        return [
            'discount' => [
                'discountReason' => $this->discountReason,
                'discountAmount' => $discountAmount,
                'subtotal' => $subTotal
            ],
            'total' => $this->format($discountAmount)
        ];
    }

    public function calc_discount($products)
    {
        $will_be_discount = $this->will_be_discount($products);

        sort($will_be_discount);

        return $this->format($will_be_discount[0], 2);
    }

    protected function will_be_discount($products)
    {
        $products = json_decode($products);

        $will_be_discount = [];

        foreach($products as $product){
            if($product->categoryId == 1 && $product->quantity >= 2){
                $will_be_discount[$product->productId] = Product::whereId($product->productId)->first()->price;
            }
        }

        return $will_be_discount;
    }

    protected function format($price)
    {
        return number_format($price , 2);
    }


}
