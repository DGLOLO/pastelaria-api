<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nome', 'email', 'telefone', 'data_nascimento',
        'endereco', 'complemento', 'bairro', 'cep',
    ];

    public function order()
    {
        return $this->hasMany(Order::class);
    }
}
