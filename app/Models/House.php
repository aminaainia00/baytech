<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class House extends Model
{
    use HasFactory, Notifiable,HasApiTokens;
    protected $guarded=[];
    public function images()
    {
       return $this->hasMany(Image::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function favoriteByUser() {
        return $this->belongsToMany(User::class,'favorites');
    }
    public function bookHouseseByUser()
    {
         return $this->belongsToMany(User::class,'books');
    }
     public function governorate ()
    {
        return $this->belongsTo(Governorate::class);
    }
     public function city ()
    {
        return $this->belongsTo(City::class);
    }
     public function books(){
          return $this->hasMany(Book::class);
    }
     public function evaluations(){
          return $this->hasMany(Evaluation::class);
    }
}
