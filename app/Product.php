<?php

namespace App;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name', 'price', 'quantity'
    ];

    public function user()
    {
    	return $this->belongsTo(User::class);
    }
}
