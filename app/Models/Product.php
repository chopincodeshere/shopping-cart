<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    parent::boot();

    static::deleting(function ($product) {
        if ($product->isForceDeleting()) {
            $product->productImages()->forceDelete();
        } else {
            $product->productImages()->delete();
        }
    });

    //TABLE
    public $table = 'products';

    //FILLABLE
    protected $fillable = [
        'admin_id',
        'name',
        'description',
        'stock',
        'price'
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
    public function productImages() {
        return $this->hasMany(ProductImage::class, "product_id", "id");
    }

    public function user() {
        return $this->belongsTo(User::class, 'admin_id', 'id');
    }

    //ATTRIBUTES
    //public function getExampleAttribute()
    //{
    //    return $data;
    //}

}
