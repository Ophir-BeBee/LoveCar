<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopToService extends Model
{
    use HasFactory;

    protected $fillable = ['shop_id','shop_service_id'];

    //connect with shop
    public function shop(){
        return $this->belongsTo(Shop::class);
    }

    //connect with shop service
    public function shop_service(){
        return $this->belongsTo(ShopService::class);
    }

    //connect with shop service items
    public function shop_service_items(){
        return $this->hasMany(ShopServiceItem::class);
    }
}
