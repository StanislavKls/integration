<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    
    protected $fillable = ['customer_id',
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

    protected $primaryKey = 'customer_id';

    public $incrementing = false;
}
