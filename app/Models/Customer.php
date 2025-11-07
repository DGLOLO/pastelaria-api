<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nome', 'email', 'telefone', 'data_nascimento',
        'endereco', 'complemento', 'bairro', 'cep',
    ];

    protected $cast =[
        'birth_date' => 'date:y-m-d',
    ];

    public function order(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
