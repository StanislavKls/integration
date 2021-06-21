<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    public $incrementing = false;

    public function orders(): mixed
    {
        return $this->hasMany(Order::class, 'shop_id');
    }
}
