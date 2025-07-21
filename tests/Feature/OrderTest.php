<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

  public function test_cria_pedido_com_um_ou_mais_produtos()
{
    $customer = Customer::factory()->create();
    $produto1 = Product::factory()->create();
    $produto2 = Product::factory()->create();

    $response = $this->postJson('/api/orders', [
        'customer_id' => $customer->id,
        'products' => [$produto1->id, $produto2->id]
    ]);

    $response->assertStatus(201);

    $this->assertDatabaseHas('orders_products', [
        'orders_id' => $response['id'],
        'products_id' => $produto1->id
    ]);
}


    public function test_falha_ao_criar_pedido_sem_produtos()
    {
        $customer = Customer::factory()->create();

        $response = $this->postJson('/api/orders', [
            'customer_id' => $customer->id,
            'products' => [] // vazio
        ]);

        $response->assertStatus(422);
    }
}

