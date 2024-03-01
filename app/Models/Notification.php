<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = ['title','description','to_user_id'];

    //connect with reads
    public function read_notifications(){
        return $this->hasMany(ReadNotification::class);
    }

    //connect with hide
    public function hide_notifications(){
        return $this->hasMany(HideNotification::class);
    }
}
