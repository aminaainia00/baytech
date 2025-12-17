<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class City extends Model
{
    use HasFactory, Notifiable,HasApiTokens;
    protected $guarded=[];
     public function governorate ()
    {
        return $this->belongsTo(Governorate::class);
    }
    public function houses(){
        return $this->hasMany(House::class);
    }
}
