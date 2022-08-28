<?php

namespace App\Utilities;


interface DiscountContract
{
    public function handle($data): array;

    public function rule($order) : bool;
}
