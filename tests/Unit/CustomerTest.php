<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;


class CustomerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected string $endpoint = "/api/customers";

    protected function setUp(): void
    {
        parent::setUp();
    }


    public function test_cria_cliente_com_sucesso(): void
    {

        $customerData = Customer::factory()->make()->toArray();

        $response = $this->postJson($this->endpoint, $customerData);


        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonFragment(['email' => $customerData['email']]);
        $this->assertDatabaseHas('customers', $customerData);
    }


    public function test_nao_cria_cliente_sem_email(): void
    {
        $customerData = Customer::factory()->make(['email' => null])->toArray();

        $response = $this->postJson($this->endpoint, $customerData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrorFor('email');
    }

    public function test_nao_cria_cliente_com_email_invalido(): void
    {

        $customerData = Customer::factory()->make(['email' => 'invalido-email'])->toArray();

        $response = $this->postJson($this->endpoint, $customerData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrorFor('email');
    }

    public function test_nao_cria_cliente_com_email_duplicado(): void
    {

        $emailDuplicado = Customer::factory()->create();
        $novoCustomerData = Customer::factory()->make([
            'email' => $emailDuplicado->email
        ])->toArray();

        $response = $this->postJson($this->endpoint, $novoCustomerData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrorFor('email');
    }

    public function test_lista_clientes(): void
    {

        Customer::factory()->count(3)->create();

        $response = $this->getJson($this->endpoint);

        //dd($response->json());

        $response->assertStatus(Response::HTTP_OK)->assertJsonCount(3);
    }

    public function test_detalha_cliente(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->getJson($this->endpoint . "/{$customer->id}");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment([
                'id' => $customer->id,
                'email' => $customer->email
            ]);
    }

    public function test_atualiza_cliente(): void
    {
        $customer = Customer::factory()->create();

        $atualizaData = [
            'nome' => 'novo nome',
            'email' => 'novoEmail@example.com'
        ];

        $response = $this->putJson("$this->endpoint/{$customer->id}", $atualizaData);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment([
                'nome' => 'novo nome',
                'email' => 'novoEmail@example.com'
            ]);

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'email' => 'novoEmail@example.com'
        ]);
    }

    public function test_exclui_cliente(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->deleteJson($this->endpoint . "/{$customer->id}");

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertSoftDeleted('customers', [
            'id' => $customer->id
        ]);

        $this->assertDatabaseMissing('customers', [
            'id' => $customer->id,
            'deleted_at' => null
        ]);
    }


    public function test_criente_nao_encontrado(): void
    {
        $response = $this->getJson($this->endpoint . "/99999");

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_criar_Cliente_sem_dados(): void
    {
        $response = $this->postJson($this->endpoint, []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
                'nome',
                'email',
                'telefone',
                'data_nascimento',
                'endereco',
                'bairro',
                'cep'
            ]);
    }
}
