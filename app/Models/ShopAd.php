<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopAd extends Model
{
    use HasFactory;

    protected $fillable = ['shop_id','image','exp_date'];

    //connect with shop
    public function shop(){
        return $this->belongsTo(Shop::class);
    }
}
