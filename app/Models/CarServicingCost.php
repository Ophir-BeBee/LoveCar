<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarServicingCost extends Model
{
    use HasFactory;

    protected $fillable = ['car_id','date','shop_name','shop_phone','shop_address','total_amount'];

    //connect with services
    public function car_services(){
        return $this->hasMany(CarService::class);
    }
}
