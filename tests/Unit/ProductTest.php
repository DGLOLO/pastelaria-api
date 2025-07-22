<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Produto;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProdutoTest extends TestCase
{
    use RefreshDatabase;

    public function test_cria_produto_com_sucesso()
    {
        $response = $this->postJson('/api/products', [
            'nome' => 'Coxinha',
            'preco' => 5.50,
            'foto' => 'coxinha.jpg',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('products', [
            'nome' => 'Coxinha',
            'preco' => 5.50,
            'foto' => 'coxinha.jpg',
        ]);
    }

    public function test_nao_cria_produto_sem_foto()
    {
        $response = $this->postJson('/api/products', [
            'nome' => 'Pastel',
            'preco' => 4.00,
        ]);

        $response->assertStatus(422); 

        $response->assertJsonValidationErrors('foto');
    }
}
