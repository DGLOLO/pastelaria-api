<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Listar todos os produtos",
     *     tags={"Products"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de produtos retornada com sucesso",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="nome", type="string", example="Pastel de Carne"),
     *                 @OA\Property(property="preco", type="number", format="float", example=8.50),
     *                 @OA\Property(property="foto", type="string", example="https://example.com/pastel-carne.jpg"),
     *                 @OA\Property(property="created_at", type="string", format="datetime", example="2024-01-01T10:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="datetime", example="2024-01-01T10:00:00.000000Z")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        return Product::all();
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Criar um novo produto",
     *     tags={"Products"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"nome","preco","foto"},
     *                 @OA\Property(property="nome", type="string", example="Pastel de Carne"),
     *                 @OA\Property(property="preco", type="number", format="float", example=8.50),
     *                 @OA\Property(property="foto", type="string", example="https://example.com/pastel-carne.jpg", description="URL da imagem ou arquivo de imagem")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Produto criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="nome", type="string", example="Pastel de Carne"),
     *             @OA\Property(property="preco", type="number", format="float", example=8.50),
     *             @OA\Property(property="foto", type="string", example="https://example.com/pastel-carne.jpg"),
     *             @OA\Property(property="created_at", type="string", format="datetime", example="2024-01-01T10:00:00.000000Z"),
     *             @OA\Property(property="updated_at", type="string", format="datetime", example="2024-01-01T10:00:00.000000Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
   public function store(Request $request)
{
    // Validação diferente para arquivo vs URL
    if ($request->hasFile('foto')) {
        $validated = $request->validate([
            'nome' => 'required',
            'preco' => 'required|numeric',
            'foto' => 'required|image'
        ]);
        
        $path = $request->file('foto')->store('products', 'public');
        $validated['foto'] = $path;
    } else {
        $validated = $request->validate([
            'nome' => 'required',
            'preco' => 'required|numeric',
            'foto' => 'required|string|url'
        ]);
    }

    $product = Product::create($validated);
    
    return response()->json($product, 201);
}

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Buscar produto por ID",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do produto",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Produto encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="nome", type="string", example="Pastel de Carne"),
     *             @OA\Property(property="preco", type="number", format="float", example=8.50),
     *             @OA\Property(property="foto", type="string", example="https://example.com/pastel-carne.jpg"),
     *             @OA\Property(property="created_at", type="string", format="datetime", example="2024-01-01T10:00:00.000000Z"),
     *             @OA\Property(property="updated_at", type="string", format="datetime", example="2024-01-01T10:00:00.000000Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Produto não encontrado"
     *     )
     * )
     */
    public function show($id)
    {
        return Product::findOrFail($id);
    }

    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     summary="Atualizar produto",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do produto",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         description="Campos para atualizar (todos opcionais)",
     *         @OA\JsonContent(
     *             @OA\Property(property="nome", type="string", example="Pastel de Queijo"),
     *             @OA\Property(property="preco", type="number", format="float", example=7.50),
     *             @OA\Property(property="foto", type="string", example="https://example.com/pastel-queijo.jpg")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Produto atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="nome", type="string", example="Pastel de Queijo"),
     *             @OA\Property(property="preco", type="number", format="float", example=7.50),
     *             @OA\Property(property="foto", type="string", example="https://example.com/pastel-queijo.jpg"),
     *             @OA\Property(property="created_at", type="string", format="datetime", example="2024-01-01T10:00:00.000000Z"),
     *             @OA\Property(property="updated_at", type="string", format="datetime", example="2024-01-01T10:00:00.000000Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Produto não encontrado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'nome'  => 'sometimes|required|string',
            'preco' => 'sometimes|required|numeric',
            'foto'  => 'sometimes|required|string',
        ]);

        $product->update($request->all());

        return response()->json($product);
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     summary="Excluir produto",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do produto",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Produto excluído com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Produto não encontrado"
     *     )
     * )
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(null, 204);
    }
}
