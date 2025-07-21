<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = ['customers_id'];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customers_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'orders_products', 'orders_id', 'products_id');
    }

}
