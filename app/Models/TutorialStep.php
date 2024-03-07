<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TutorialStep extends Model
{
    use HasFactory;

    protected $fillable = ['tutorial_id','step_title','step_description','step_image'];

    //connect with tutorial
    public function tutorial(){
        return $this->belongsTo(Tutorial::class);
    }
}
