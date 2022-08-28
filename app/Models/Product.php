<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public static function setStock($products)
    {
        collect($products)->each(function($product){
            self::whereId($product['productId'])->decrement('stock',$product['quantity']);
        });
    }
}
