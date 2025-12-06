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
    public function views()
    {
        return $this->hasMany(View::class);
    }
     public function books()
    {
        return $this->hasMany(Book::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
