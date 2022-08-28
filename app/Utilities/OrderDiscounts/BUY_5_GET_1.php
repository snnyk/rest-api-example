<?php

namespace App\Utilities\OrderDiscounts;

use App\Models\Product;
use App\Utilities\DiscountContract;


class BUY_5_GET_1 implements DiscountContract
{

    protected $discountReason = 'BUY_5_GET_1';

    protected $orderDiscount;

    protected $totalDiscount;


    public function __construct($orderDiscount, $totalDiscount)
    {
        $this->orderDiscount = $orderDiscount;

        $this->totalDiscount = $totalDiscount;

    }

    public function rule($order) : bool
    {
        $deserve = false;

        $will_be_discount = $this->will_be_discount($order->items);

        //turn deserve para to true if any product have more than 6 quantity
        foreach($will_be_discount as $productId => $quantity){
            if($quantity >= 6){
                $deserve = true;
            }
        }

        return $deserve;
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
        $discount = 0;

        $will_be_discount = $this->will_be_discount($products);

        foreach($will_be_discount as $productId => $quantity){
            $discount += Product::whereId($productId)->first()->price;
        }

        return $this->format($discount, 2);
    }

    protected function will_be_discount($products)
    {
        $products = json_decode($products);

        $will_be_discount = [];

        foreach($products as $product){
            if($product->categoryId == 2){
                //create if not  exist
                if(!isset($will_be_discount[$product->productId])){
                    $will_be_discount[$product->productId] = $product->quantity;
                }else{
                    $will_be_discount[$product->productId] += $product->quantity;
                }
            }
        }

        return $will_be_discount;
    }

    protected function format($price)
    {
        return number_format($price , 2);
    }


}
