<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmation;

class OrderController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/orders",
     *     summary="Listar todos os pedidos",
     *     tags={"Orders"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de pedidos retornada com sucesso",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="customer_id", type="integer", example=1),
     *                 @OA\Property(property="created_at", type="string", format="datetime", example="2024-01-01T10:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="datetime", example="2024-01-01T10:00:00.000000Z"),
     *                 @OA\Property(
     *                     property="customer",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="nome", type="string", example="João Silva"),
     *                     @OA\Property(property="email", type="string", example="joao@email.com"),
     *                     @OA\Property(property="telefone", type="string", example="11999999999")
     *                 ),
     *                 @OA\Property(
     *                     property="products",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Pastel de Carne"),
     *                         @OA\Property(property="price", type="number", format="float", example=8.50),
     *                         @OA\Property(property="photo", type="string", example="https://example.com/pastel-carne.jpg")
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        return Order::with(['customer', 'products'])->get();
    }

    /**
     * @OA\Post(
     *     path="/api/orders",
     *     summary="Criar um novo pedido",
     *     tags={"Orders"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"customer_id","products"},
     *             @OA\Property(property="customer_id", type="integer", example=1, description="ID do cliente"),
     *             @OA\Property(
     *                 property="products",
     *                 type="array",
     *                 description="Array de IDs dos produtos",
     *                 @OA\Items(type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Pedido criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="customer_id", type="integer", example=1),
     *             @OA\Property(property="created_at", type="string", format="datetime", example="2024-01-01T10:00:00.000000Z"),
     *             @OA\Property(property="updated_at", type="string", format="datetime", example="2024-01-01T10:00:00.000000Z"),
     *             @OA\Property(
     *                 property="products",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Pastel de Carne"),
     *                     @OA\Property(property="price", type="number", format="float", example=8.50),
     *                     @OA\Property(property="photo", type="string", example="https://example.com/pastel-carne.jpg"),
     *                     @OA\Property(
     *                         property="pivot",
     *                         type="object",
     *                         @OA\Property(property="order_id", type="integer", example=1),
     *                         @OA\Property(property="product_id", type="integer", example=1)
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="customer_id",
     *                     type="array",
     *                     @OA\Items(type="string", example="The selected customer id is invalid.")
     *                 ),
     *                 @OA\Property(
     *                     property="products.0",
     *                     type="array",
     *                     @OA\Items(type="string", example="The selected products.0 is invalid.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'products' => 'required|array',
            'products.*' => 'exists:products,id',
        ]);

        $order = Order::create([
            'customers_id' => $request->customer_id,
        ]);

        $order->products()->attach($request->products);

        // Send email notification with order details
        Mail::to($order->customer->email)->send(new OrderConfirmation($order));

        return response()->json($order->load('products'), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/orders/{id}",
     *     summary="Buscar pedido por ID",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do pedido",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pedido encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="customer_id", type="integer", example=1),
     *             @OA\Property(property="created_at", type="string", format="datetime", example="2024-01-01T10:00:00.000000Z"),
     *             @OA\Property(property="updated_at", type="string", format="datetime", example="2024-01-01T10:00:00.000000Z"),
     *             @OA\Property(
     *                 property="customer",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="nome", type="string", example="João Silva"),
     *                 @OA\Property(property="email", type="string", example="joao@email.com"),
     *                 @OA\Property(property="telefone", type="string", example="11999999999"),
     *                 @OA\Property(property="data_nascimento", type="string", format="date", example="1990-01-01"),
     *                 @OA\Property(property="endereco", type="string", example="Rua das Flores, 123"),
     *                 @OA\Property(property="bairro", type="string", example="Centro"),
     *                 @OA\Property(property="cep", type="string", example="01234-567"),
     *                 @OA\Property(property="complemento", type="string", nullable=true, example="Apto 101")
     *             ),
     *             @OA\Property(
     *                 property="products",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Pastel de Carne"),
     *                     @OA\Property(property="price", type="number", format="float", example=8.50),
     *                     @OA\Property(property="photo", type="string", example="https://example.com/pastel-carne.jpg"),
     *                     @OA\Property(
     *                         property="pivot",
     *                         type="object",
     *                         @OA\Property(property="order_id", type="integer", example=1),
     *                         @OA\Property(property="product_id", type="integer", example=1)
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Pedido não encontrado"
     *     )
     * )
     */
    public function show($id)
    {
        return Order::with(['products', 'customer'])->findOrFail($id);
    }

    /**
     * @OA\Delete(
     *     path="/api/orders/{id}",
     *     summary="Excluir pedido",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do pedido",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Pedido excluído com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Pedido não encontrado"
     *     )
     * )
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return response()->json(null, 204);
    }
}