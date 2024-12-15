<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    const PENDING = "PENDING",
        COMPLETED = "COMPLETED";

    //public $timestamps = false;

    //TABLE
    public $table = 'orders';

    //FILLABLE
    protected $fillable = [
        'user_id',
        'status',
        'is_paid'
    ];

    //HIDDEN
    protected $hidden = [];

    //APPENDS
    protected $appends = [];

    //WITH
    protected $with = [];

    //CASTS
    protected $casts = [];

    //RELATIONSHIPS
    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function order_items() {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }
    //ATTRIBUTES
    //public function getExampleAttribute()
    //{
    //    return $data;
    //}

}
