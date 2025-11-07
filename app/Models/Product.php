<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = ['nome', 'preco', 'foto', 'type'];

    protected $cast =[
        'preco'=>'decimal:2',
    ];

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'orders_products', 'products_id', 'orders_id')->withTimestamps();
    }

    public function getPhotoUrlAttribute (): String|null{
        if ($this-> foto){
            return Storage::disk('public')->url($this->foto);
        }
        return null;
    }
}
