<?php

namespace App\Http\Controllers;

use App\Http\Requests\DiscountRequest;
use App\Models\Order;
use App\Utilities\DiscountBuilder;

class DiscountController extends Controller
{

    public function index(DiscountRequest $request)
    {
        $discounts = new DiscountBuilder($request->id);
        $discounts->apply();

        return [
            'orderId' => $request->id,
            'discounts' => $discounts->orderDiscounts(),
            'totalDiscount' => $discounts->totalDiscount(),
            'discountedTotal' => $discounts->discountedTotal()
        ];
    }
}
