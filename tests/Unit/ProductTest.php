<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use App\Models\Product;
use Symfony\Component\HttpFoundation\Response;

class ProductTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected string $endpoint = '/api/products';

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_lista_produtos_com_sucesso(): void
    {
        Product::factory()->count(3)->create();

        $response = $this->getJson($this->endpoint);

        $response->assertStatus(Response::HTTP_OK)->assertJsonCount(3);
    }

    public function test_cria_Product_com_foto_com_sucesso(): void
    {
        $ProductData = Product::factory()->make()->toArray();

        $nomeFoto = basename($ProductData['foto']);
        $ProductData['foto'] = UploadedFile::fake()->image($nomeFoto);

        $response = $this->post($this->endpoint, $ProductData);

        $response->assertStatus(Response::HTTP_CREATED)
                 ->assertJsonFragment(['nome' => $ProductData['nome']]);

        $this->assertDatabaseHas('products', ['nome' => $ProductData['nome'], 'id' => $response['data']['id']]);
    }

    public function test_nao_cria_Product_sem_foto(): void
    {
        $ProductData = Product::factory()->make(['foto' => null])->toArray();

        $response = $this->postJson($this->endpoint, $ProductData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                 ->assertJsonValidationErrors(['foto']);
    }

    public function test_nao_cria_Product_com_foto_invalida(): void
    {
        $ProductData = Product::factory()->make()->toArray();
        // Foto como string, não um arquivo
        $ProductData['foto'] = 'invalid_string';

        $response = $this->postJson($this->endpoint, $ProductData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                 ->assertJsonValidationErrors(['foto']);
    }

    public function test_nao_cria_Product_com_preco_invalido(): void
    {
        $ProductData = Product::factory()->make()->toArray();

        $ProductData['foto'] = UploadedFile::fake()->image(basename($ProductData['foto']));
        $ProductData['preco'] = 'preco-invalido';

        $response = $this->postJson($this->endpoint, $ProductData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                 ->assertJsonValidationErrors(['preco']);
    }

    public function test_nao_cria_Product_sem_campos_obrigatorios(): void
    {
        $response = $this->postJson($this->endpoint, []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                 ->assertJsonValidationErrors(['nome', 'preco', 'foto']);
    }

    public function test_mostra_detalhes_do_Product(): void
    {
        $Product = Product::factory()->create();

        $response = $this->getJson("{$this->endpoint}/{$Product->id}");

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJsonFragment(['id' => (string) $Product->id, 'nome' => $Product->nome]);
    }

    public function test_mostra_Product_invalido(): void
    {
        $response = $this->getJson("{$this->endpoint}/999999");

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_atualiza_Product_com_sucesso(): void
    {
        $Product = Product::factory()->create();

        $novoNome = $this->faker->words(3, true);
        $novoPreco = $this->faker->randomFloat(2, 10, 1000);

        $dadosAtualizados = [
            'nome' => $novoNome,
            'preco' => $novoPreco,
        ];

        $response = $this->putJson("{$this->endpoint}/{$Product->id}", $dadosAtualizados);

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJsonFragment(['nome' => $novoNome, 'preco' => $novoPreco]);

        $this->assertDatabaseHas('products', ['id' => $Product->id, 'nome' => $novoNome, 'preco' => $novoPreco]);
    }

    public function test_deleta_Product_com_soft_delete(): void
    {
        $Product = Product::factory()->create();

        $response = $this->deleteJson("{$this->endpoint}/{$Product->id}");

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertSoftDeleted('products', ['id' => $Product->id]);
        $this->assertDatabaseMissing('products', ['id' => $Product->id, 'deleted_at' => null]);
    }
}
