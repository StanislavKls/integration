<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    
    protected $fillable = ['id',
                           'display_name',
                           'birth_date',
                           'phone',
                           'points',
                           'discount_rate',
                           'cashback_rate',
                           'membership_tier_name',
                           'date_created',
                           'last_transaction_time',
                           'uid'
                          ];

    protected $primaryKey = 'id';

    public $incrementing = false;

    public function orders(): mixed
    {
        return $this->hasMany(Order::class, 'customer_id');
    }
}
