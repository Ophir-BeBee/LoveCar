<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomeDate extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','date'];

    //connect with incomes
    public function incomes(){
        return $this->hasMany(Income::class,'income_date_id');
    }
}
