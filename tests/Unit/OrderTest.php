<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Order;
use App\Mail\OrderConfirmation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected string $endpoint = "/api/orders";

    protected function setUp(): void
    {
        parent::setUp();
    }



    public function test_cria_pedido_com_um_ou_mais_produtos()
    {
        $customer = Customer::factory()->create();
        $produto1 = Product::factory()->create();
        $produto2 = Product::factory()->create();

        $orderData = [
            'customer_id' => $customer->id,
            'items' => [
                ['product_id' => $produto1->id, 'quantity' => 2],
                ['product_id' => $produto2->id, 'quantity' => 1]
            ]
        ];


         
        

        $response = $this->postJson($this->endpoint, $orderData);

        //dd($response->json());

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonPath('data.customer_id', $customer->id)
            ->assertJsonPath('data.status', 'created');


        
    }


    public function test_falha_ao_criar_pedido_sem_produtos()
    {
        $customer = Customer::factory()->create();

        $response = $this->postJson($this->endpoint, [
            'customer_id' => $customer->id,
            'items' => []
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('items');
    }

    public function test_criar_pedido_sem_cliente(): void
    {
        $product = Product::factory()->create();

        $response = $this->postJson($this->endpoint, [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1],
            ],
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('customer_id');
    }

    public function test_Campos_obrigadorios_do_pedido_faltado(): void
    {
        $response = $this->postJson($this->endpoint, []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
                'customer_id',
                'items'
            ]);
    }


    public function test_detalha_pedido_existente(): void
    {
        $orderData = Order::factory()->create();

        $response = $this->getJson($this->endpoint . "/{$orderData->id}");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(
                [
                    'id' => $orderData->id
                ]
            );
    }

    public function test_detalha_pedido_inexistente(): void
    {
        $response = $this->getJson($this->endpoint . "/99999");

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_exclui_pedido_com_sucesso(): void
    {
        $orderData = Order::factory()->create();

        $response = $this->deleteJson($this->endpoint . "/{$orderData->id}");

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertSoftDeleted('orders', [
            'id' => $orderData->id
        ]);
    }

    public function test_lista_pedidos(): void
    {
        Order::factory()->count(3)->create();

        $response = $this->getJson($this->endpoint);

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJsonPath('per_page', 15)
                 ->assertJsonPath('current_page', 1)
                 ->assertJsonPath('total', 3)
                 ->assertJsonCount(3, 'data');
    }

    public function test_lista_pedidos_com_paginacao(): void
    {
        // Criar 25 pedidos
        Order::factory()->count(25)->create();

        // Testar primeira página (padrão: 15 itens)
        $response = $this->getJson($this->endpoint);

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJsonPath('per_page', 15)
                 ->assertJsonPath('current_page', 1)
                 ->assertJsonPath('total', 25)
                 ->assertJsonPath('last_page', 2)
                 ->assertJsonCount(15, 'data');

        // Testar segunda página
        $response = $this->getJson($this->endpoint . '?page=2');

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJsonPath('current_page', 2)
                 ->assertJsonCount(10, 'data'); // 25 - 15 = 10

        // Testar per_page customizado
        $response = $this->getJson($this->endpoint . '?per_page=5');

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJsonPath('per_page', 5)
                 ->assertJsonCount(5, 'data');
    }

    public function test_criar_pedido_com_cliente_invalido(): void
    {
        $idClienteInvalido = 99999;

        $product = Product::factory()->create();

        $orderData = [
            'customer_id' => $idClienteInvalido,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1],
            ],
        ];

        $response = $this->postJson($this->endpoint, $orderData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('customer_id');
    }


    public function test_preco_invalido(): void
    {
        $product = Product::factory()->create(['preco' => 125.98]);

        $customer = Customer::factory()->create();

        $orderData = [
            'customer_id' => $customer->id,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1]
            ]
        ];

        $response = $this->postJson($this->endpoint, $orderData);

        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas('orders_products', [
            'orders_id' => $response->json('data.id'),
            'products_id' => $product->id,
        ]);
    }


    public function test_pedido_criado_email_enviado(): void
    {

        Mail::fake();

        $customer = Customer::factory()->create();
        $product = Product::factory()->create();

        $orderData = [
             'customer_id' => $customer->id,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1]
            ]
        ];

        $response = $this->postJson($this->endpoint, $orderData);

        $response->assertStatus(Response::HTTP_CREATED);

        Mail::assertQueued(OrderConfirmation::class, function ($mail) use ($customer) {
            return $mail->hasTo($customer->email);
                
        });

       
        
       
    }


    public function test_email_mensagem_sucesso(): void
    {
        Mail::fake();

        $email = "testeEmail@testeEmail.com";

        $customer = Customer::factory()->create(['email' => $email]);
        $product = Product::factory()->create(['preco' => 125.98]);

        $orderData = [
            'customer_id' => $customer->id,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1]
            ],
        ];

        $response = $this->postJson($this->endpoint, $orderData);


        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonPath('data.customer.email', $email)
            ->assertJsonPath('message', 'Pedido criado com sucesso.');

        $this->assertDatabaseHas('orders', [
            'customers_id' => $customer->id,
            'id' => $response->json('data.id')
        ]);


        Mail::assertQueued(OrderConfirmation::class);
    }
}
