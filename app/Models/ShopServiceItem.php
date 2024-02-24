<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopServiceItem extends Model
{
    use HasFactory;

    protected $fillable = ['shop_id','shop_service_id','name'];

    //connect with shop
    public function shop(){
        return $this->belongsTo(Shop::class);
    }

    //connect with shop service
    public function shop_service(){
        return $this->belongsTo(ShopService::class);
    }

}
