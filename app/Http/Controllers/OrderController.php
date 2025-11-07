<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Mail\OrderConfirmation;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/orders",
     *     summary="Listar todos os pedidos (paginado)",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Número da página",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Itens por página (máximo 100)",
     *         required=false,
     *         @OA\Schema(type="integer", example=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de pedidos retornada com sucesso (paginado)",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="customers_id", type="integer", example=1),
     *                     @OA\Property(property="created_at", type="string", format="datetime", example="2024-01-01T10:00:00.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="datetime", example="2024-01-01T10:00:00.000000Z"),
     *                     @OA\Property(
     *                         property="customer",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="nome", type="string", example="João Silva"),
     *                         @OA\Property(property="email", type="string", example="joao@email.com"),
     *                         @OA\Property(property="telefone", type="string", example="11999999999")
     *                     ),
     *                     @OA\Property(
     *                         property="products",
     *                         type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="nome", type="string", example="Pastel de Carne"),
     *                             @OA\Property(property="preco", type="number", format="float", example=8.50),
     *                             @OA\Property(property="foto", type="string", example="https://example.com/pastel-carne.jpg")
     *                         )
     *                     )
     *                 )
     *             ),
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(property="first_page_url", type="string", example="http://localhost/api/orders?page=1"),
     *             @OA\Property(property="from", type="integer", example=1),
     *             @OA\Property(property="last_page", type="integer", example=5),
     *             @OA\Property(property="last_page_url", type="string", example="http://localhost/api/orders?page=5"),
     *             @OA\Property(property="links", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="url", type="string", nullable=true),
     *                     @OA\Property(property="label", type="string"),
     *                     @OA\Property(property="active", type="boolean")
     *                 )
     *             ),
     *             @OA\Property(property="next_page_url", type="string", nullable=true, example="http://localhost/api/orders?page=2"),
     *             @OA\Property(property="path", type="string", example="http://localhost/api/orders"),
     *             @OA\Property(property="per_page", type="integer", example=15),
     *             @OA\Property(property="prev_page_url", type="string", nullable=true),
     *             @OA\Property(property="to", type="integer", example=15),
     *             @OA\Property(property="total", type="integer", example=75)
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $perPage = min(max(1, (int) $perPage), 100);
        
        $orders = Order::with(['customer', 'products'])->paginate($perPage);
        $orders->getCollection()->transform(function ($order) {
            return new OrderResource($order);
        });
        
        return $orders;
    }

    /**
     * @OA\Post(
     *     path="/api/orders",
     *     summary="Criar um novo pedido",
     *     tags={"Orders"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"customer_id","items"},
     *             @OA\Property(property="customer_id", type="integer", example=1, description="ID do cliente"),
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 description="Array de itens do pedido",
     *                 @OA\Items(
     *                     @OA\Property(property="product_id", type="integer", example=1),
     *                     @OA\Property(property="quantity", type="integer", example=2, description="Quantidade do produto")
     *                 )
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
    public function store(StoreOrderRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $order = Order::create([
                'customers_id' => $request->customer_id,
                'status' => 'created',
            ]);

            $itemsToAttach = [];
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                $itemsToAttach[$product->id] = [
                    'quantidade' => $item['quantity'],
                    'valorCompra' => $product->preco,
                ];
            }

            $order->products()->attach($itemsToAttach);
            $order->load(['customer', 'products']);
            Mail::to($order->customer->email)->queue(new OrderConfirmation($order));

            return (new OrderResource($order))
                ->additional(['message' => 'Pedido criado com sucesso.'])
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);
        });
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
        $order = Order::with(['products', 'customer'])->findOrFail($id);
        return new OrderResource($order);
    }







    /**
     * @OA\Put(
     *     path="/api/orders/{id}",
     *     summary="Atualizar um pedido existente",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do pedido a ser atualizado",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
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
     *         response=200,
     *         description="Pedido atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Pedido atualizado com sucesso."),
     *             @OA\Property(property="order", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="customer_id", type="integer", example=1),
     *                 @OA\Property(property="created_at", type="string", format="datetime", example="2024-01-01T10:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="datetime", example="2024-01-01T10:00:00.000000Z"),
     *                 @OA\Property(
     *                     property="customer",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="nome", type="string", example="João Silva"),
     *                     @OA\Property(property="email", type="string", example="joao@email.com")
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
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Pedido não encontrado"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor"
     *     )
     * )
     */
    public function update(UpdateOrderRequest $request, $id)
    {
        $order = Order::findOrFail($id);

        return DB::transaction(function () use ($request, $order) {
            if ($request->has('status')) {
                $order->update(['status' => $request->status]);
            }

            if ($request->has('items')) {
                $itemsToSync = [];
                foreach ($request->items as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    
                    $itemsToSync[$product->id] = [
                        'quantidade' => $item['quantity'],
                        'valorCompra' => $product->preco,
                    ];
                }
                $order->products()->sync($itemsToSync);
            }

            $order->load(['customer', 'products']);

            return (new OrderResource($order))
                ->additional(['message' => 'Pedido atualizado com sucesso.'])
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        });
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
