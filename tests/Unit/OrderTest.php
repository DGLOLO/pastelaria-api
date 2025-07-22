<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Order;
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
            'products' => [$produto1->id, $produto2->id]
        ];


         
        

        $response = $this->postJson($this->endpoint, $orderData);

        //dd($response->json());

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonFragment([
                'customer_id' => (string) $customer->id
            ]);


        
    }


    public function test_falha_ao_criar_pedido_sem_produtos()
    {
        $customer = Customer::factory()->create();

        $response = $this->postJson($this->endpoint, [
            'customer_id' => $customer->id,
            'products' => []
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('products');
    }

    public function test_criar_pedido_sem_cliente(): void
    {
        $product = Product::factory()->create();

        $response = $this->postJson($this->endpoint, [
            'products' => [
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
                'products'
            ]);
    }


    public function test_detalha_pedido_existente(): void
    {
        $orderData = Order::factory()->create();

        $response = $this->getJson($this->endpoint . "/{$orderData->id}");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(
                [
                    'id' => (string) $orderData->id
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

        $response->assertStatus(Response::HTTP_OK)->assertJsonCount(3);
    }

    public function test_criar_pedido_com_cliente_invalido(): void
    {
        $idClienteInvalido = 99999;

        $product = Product::factory()->create();

        $orderData = [
            'customer_id' => $idClienteInvalido,
            'products' => [
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
            'products' => [
                [
                    'id' => $product->id,
                    'quantity' => 1,
                    //'price_at_purchase' => 'invalid-price'

                ],
            ],
        ];

        $response = $this->postJson($this->endpoint, $orderData);

        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas('orders_product', [
            'order_id' => $response->json('data.id'),
            'product_id' => $product->id,
            'price_at_purchase' => $product->preco,
        ]);
    }


    public function test_pedido_criado_email_enviado(): void
    {

        Mail::fake();

        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['preco' => 125.98]);

        $orderData = [
            'customer_id' => $customer->id,
            'products' => [
                ['product_id' => $product->id, 'quantity' => 1],
            ],
        ];

        $response = $this->postJson($this->endpoint, $orderData);

        $response->assertStatus(Response::HTTP_CREATED);

        Mail::assertSent(OrderCreatedMail::class, function ($mail) use ($customer, $response) {
            return $mail->hasTo($customer->email) &&
                $mail->order->id === $response->json('data.id');
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
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,

                ],
            ]
        ];

        $response = $this->postJson($this->endpoint, $orderData);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonPath('data.customer.email', $email)
            ->assertJsonFragment(["message" => "Pedido criado Com  sucesso."]);

        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id,
            'id' => $response->json('data.id')
        ]);


        Mail::assertSent(OrderCreatedMail::class);
    }
}
