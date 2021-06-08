<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $fillable = ['id', 'date_created', 'points', 'certificate_points',
                           'cash', 'total', 'comment', 'delivery_type', 'customer_id', 'shop_id', 'delivery_address',
                           'delivery_receiver_name', 'delivery_receiver_phone', 'delivery_user_comment'];

    public $incrementing = false;

    public function items()
    {
        return $this->belongsToMany(Item::class, 'order_item', 'order_id', 'item_id')
            ->withPivot('price', 'qty', 'variant_name');
    }
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

}
