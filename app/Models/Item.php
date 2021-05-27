<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'name', 'price', 'type', 'variant_name', 'external_id'];

    protected $primaryKey = 'id';

    public $incrementing = false;

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_item', 'item_id', 'order_id');
    }
}
