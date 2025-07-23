<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
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

    public function test_cria_produtos_com_foto_com_sucesso(): void
    {

        Storage::fake('public');

        $file = UploadedFile::fake()->image('products.jpg');

        //$nomeFoto = basename($ProductData['foto']);
        $ProductData = [
            'nome' => 'nome produto',
            'preco' => 125.98,
            'foto' => $file
        ];

        $response = $this-> call ('post', $this->endpoint, $ProductData,[],['foto'=> $file]);



       

        $response->assertStatus(Response::HTTP_CREATED)
                 ->assertJsonFragment(['nome' => $ProductData['nome']]);

        // Verifica se o arquivo foi salvo
        $this->assertDatabaseHas('products', [
            'nome' => $ProductData['nome'],
            'preco' => $ProductData['preco'],
            'foto' => 'products/' . $ProductData['foto']->hashName()
        ]);
    }

    public function test_nao_cria_produtos_sem_foto(): void
    {
        $ProductData = Product::factory()->make(['foto' => null])->toArray();

        $response = $this->postJson($this->endpoint, $ProductData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                 ->assertJsonValidationErrors(['foto']);
    }

    public function test_nao_cria_produtos_com_foto_invalida(): void
    {
        $ProductData = Product::factory()->make()->toArray();
       
        $ProductData['foto'] = UploadedFile::fake()->create('documentos.pdf',1000);

        $response = $this->postJson($this->endpoint, $ProductData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                 ->assertJsonValidationErrors(['foto']);
    }

    public function test_nao_cria_produtos_com_preco_invalido(): void
    {
        $ProductData = Product::factory()->make()->toArray();

        $ProductData['foto'] = UploadedFile::fake()->image(basename($ProductData['foto']));
        $ProductData['preco'] = 'preco-invalido';

        $response = $this->postJson($this->endpoint, $ProductData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                 ->assertJsonValidationErrors(['preco']);
    }

    public function test_nao_cria_produtos_sem_campos_obrigatorios(): void
    {
        $response = $this->postJson($this->endpoint, []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                 ->assertJsonValidationErrors(['nome', 'preco', 'foto']);
    }

    public function test_mostra_detalhes_do_produtos(): void
    {
        $Product = Product::factory()->create();

        $response = $this->getJson("{$this->endpoint}/{$Product->id}");

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJsonFragment(['id' => $Product->id, 'nome' => $Product->nome]);
    }

    public function test_mostra_produtos_invalido(): void
    {
        $response = $this->getJson("{$this->endpoint}/999999");

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_atualiza_produtos_com_sucesso(): void
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

    public function test_deleta_produtos_com_soft_delete(): void
    {
        $Product = Product::factory()->create();

        $response = $this->deleteJson("{$this->endpoint}/{$Product->id}");

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertSoftDeleted('products', ['id' => $Product->id]);
        $this->assertDatabaseMissing('products', ['id' => $Product->id, 'deleted_at' => null]);
    }
}
