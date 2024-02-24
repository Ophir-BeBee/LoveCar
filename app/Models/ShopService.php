<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopService extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    //connect with items
    public function shop_service_items(){
        return $this->hasMany(ShopServiceItem::class);
    }
}
