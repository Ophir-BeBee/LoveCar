<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopToCategory extends Model
{
    use HasFactory;

    protected $fillable = ['shop_id','shop_category_id'];

    //connect with shop
    public function shop(){
        return $this->belongsTo(Shop::class);
    }

    //connect with category
    public function shop_category(){
        return $this->belongsTo(ShopCategory::class);
    }
}
