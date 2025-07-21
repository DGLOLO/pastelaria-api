<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_cria_cliente_com_sucesso()
    {
        $response = $this->postJson('/api/customers', [
            'nome'            => 'João da Silva',
            'email'           => 'joao@example.com',
            'telefone'        => '11999999999',
            'data_nascimento' => '1990-01-01',
            'endereco'        => 'Rua A',
            'bairro'          => 'Centro',
            'cep'             => '12345678',
            'complemento'     => null, // ou algum valor opcional
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('customers', [
            'email' => 'joao@example.com',
        ]);
    }
}
