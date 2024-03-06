<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckListCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    //connect with check list
    public function check_lists(){
        return $this->hasMany(CheckList::class,'category_id');
    }
}
