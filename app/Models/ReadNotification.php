<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReadNotification extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','notification_id'];

    //connect with notification
    public function notification(){
        return $this->belongsTo(Notification::class);
    }
}
