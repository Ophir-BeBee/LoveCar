<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopImage extends Model
{
    use HasFactory;

    protected $fillable = ['shop_id','name'];

    //connect with shop
    public function shop(){
        return $this->belongsTo(Shop::class);
    }
}
