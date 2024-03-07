<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TutorialStep extends Model
{
    use HasFactory;

    protected $fillable = ['tutorial_id','step_label','step_image'];

    //connect with tutorial
    public function tutorial(){
        return $this->belongsTo(Tutorial::class);
    }
}
