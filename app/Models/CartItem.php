<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    //public $timestamps = false;

    //TABLE
    public $table = 'cart_items';

    //FILLABLE
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity'
    ];

    //HIDDEN
    protected $hidden = [];

    //APPENDS
    protected $appends = [
        'item_total'
    ];

    //WITH
    protected $with = [];

    //CASTS
    protected $casts = [];

    //RELATIONSHIPS
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function product() {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    //ATTRIBUTES
    public function getItemTotalAttribute() {
        return $this->product->price * $this->quantity;
    }

}
