<?php

namespace Tests\Feature;

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

    public function test_nao_cria_cliente_com_email_duplicado(): void {

        $emailDuplicado = Customer::factory()->create();
        $novoCustomerData = Customer::factory()->make([
            'email' => $emailDuplicado-> email])->toArray();

        $response = $this->postJson($this->endpoint, $novoCustomerData);

        $response -> assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            -> assertJsonValidationErrorFor('email');
    }

    public function test_lista_clientes(): void
    {

        Customer::factory()->count(3)->create();

        $response = $this->getJson($this->endpoint);

        //dd($response->json());

        $response->assertStatus(Response::HTTP_OK)->assertJsonCount(3);
    }
}
