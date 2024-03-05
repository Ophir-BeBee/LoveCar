<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarService extends Model
{
    use HasFactory;

    protected $fillable = ['car_servicing_cost_id','type','particular','condition','brand','model','price','quantity','amount','guarantee_value','guarantee_type','start_date','end_date'];

    //connect with car service cost
    public function car_servicing_cost(){
        return $this->belongsTo(CarServicingCost::class);
    }
}
