<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = ['item_id', 'name', 'price', 'qty', 'sku', 'type', 'variant_name', 'external_id'];

    protected $primaryKey = 'item_id';

    public $incrementing = false;

    public $timestamps = false;
}
