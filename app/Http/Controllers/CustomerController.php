<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/customers",
     *     summary="Listar todos os clientes",
     *     tags={"Customers"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de clientes retornada com sucesso",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="nome", type="string", example="João Silva"),
     *                 @OA\Property(property="email", type="string", example="joao@email.com"),
     *                 @OA\Property(property="telefone", type="string", example="11999999999"),
     *                 @OA\Property(property="data_nascimento", type="string", format="date", example="1990-01-01"),
     *                 @OA\Property(property="endereco", type="string", example="Rua das Flores, 123"),
     *                 @OA\Property(property="bairro", type="string", example="Centro"),
     *                 @OA\Property(property="cep", type="string", example="01234-567"),
     *                 @OA\Property(property="complemento", type="string", example="Apto 101")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        return Customer::all();
    }

    /**
     * @OA\Post(
     *     path="/api/customers",
     *     summary="Criar um novo cliente",
     *     tags={"Customers"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome","email","telefone","data_nascimento","endereco","bairro","cep"},
     *             @OA\Property(property="nome", type="string", example="João Silva"),
     *             @OA\Property(property="email", type="string", example="joao@email.com"),
     *             @OA\Property(property="telefone", type="string", example="11999999999"),
     *             @OA\Property(property="data_nascimento", type="string", format="date", example="1990-01-01"),
     *             @OA\Property(property="endereco", type="string", example="Rua das Flores, 123"),
     *             @OA\Property(property="bairro", type="string", example="Centro"),
     *             @OA\Property(property="cep", type="string", example="01234-567"),
     *             @OA\Property(property="complemento", type="string", nullable=true, example="Apto 101")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Cliente criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="nome", type="string", example="João Silva"),
     *             @OA\Property(property="email", type="string", example="joao@email.com"),
     *             @OA\Property(property="telefone", type="string", example="11999999999"),
     *             @OA\Property(property="data_nascimento", type="string", format="date", example="1990-01-01"),
     *             @OA\Property(property="endereco", type="string", example="Rua das Flores, 123"),
     *             @OA\Property(property="bairro", type="string", example="Centro"),
     *             @OA\Property(property="cep", type="string", example="01234-567"),
     *             @OA\Property(property="complemento", type="string", nullable=true, example="Apto 101")
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
        $request->validate([
            'nome'            => 'required|string',
            'email'           => 'required|email|unique:customers,email',
            'telefone'        => 'required|string',
            'data_nascimento' => 'required|date',
            'endereco'        => 'required|string',
            'bairro'          => 'required|string',
            'cep'             => 'required|string',
            'complemento'     => 'nullable|string',
        ]);

        $customer = Customer::create($request->all());
        
        return response()->json($customer, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/customers/{id}",
     *     summary="Buscar cliente por ID",
     *     tags={"Customers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do cliente",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cliente encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="nome", type="string", example="João Silva"),
     *             @OA\Property(property="email", type="string", example="joao@email.com"),
     *             @OA\Property(property="telefone", type="string", example="11999999999"),
     *             @OA\Property(property="data_nascimento", type="string", format="date", example="1990-01-01"),
     *             @OA\Property(property="endereco", type="string", example="Rua das Flores, 123"),
     *             @OA\Property(property="bairro", type="string", example="Centro"),
     *             @OA\Property(property="cep", type="string", example="01234-567"),
     *             @OA\Property(property="complemento", type="string", nullable=true, example="Apto 101")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cliente não encontrado"
     *     )
     * )
     */
    public function show($id)
    {
        return Customer::findOrFail($id);
    }

    /**
     * @OA\Put(
     *     path="/api/customers/{id}",
     *     summary="Atualizar cliente",
     *     tags={"Customers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do cliente",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         description="Campos para atualizar (todos opcionais)",
     *         @OA\JsonContent(
     *             @OA\Property(property="nome", type="string", example="João Silva"),
     *             @OA\Property(property="email", type="string", example="joao@email.com"),
     *             @OA\Property(property="telefone", type="string", example="11999999999"),
     *             @OA\Property(property="data_nascimento", type="string", format="date", example="1990-01-01"),
     *             @OA\Property(property="endereco", type="string", example="Rua das Flores, 123"),
     *             @OA\Property(property="bairro", type="string", example="Centro"),
     *             @OA\Property(property="cep", type="string", example="01234-567"),
     *             @OA\Property(property="complemento", type="string", nullable=true, example="Apto 101")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cliente atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="nome", type="string", example="João Silva"),
     *             @OA\Property(property="email", type="string", example="joao@email.com"),
     *             @OA\Property(property="telefone", type="string", example="11999999999"),
     *             @OA\Property(property="data_nascimento", type="string", format="date", example="1990-01-01"),
     *             @OA\Property(property="endereco", type="string", example="Rua das Flores, 123"),
     *             @OA\Property(property="bairro", type="string", example="Centro"),
     *             @OA\Property(property="cep", type="string", example="01234-567"),
     *             @OA\Property(property="complemento", type="string", nullable=true, example="Apto 101")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cliente não encontrado"
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
        $customer = Customer::findOrFail($id);

        $request->validate([
            'nome'            => 'sometimes|required|string',
            'email'           => 'sometimes|required|email|unique:customers,email,' . $customer->id,
            'telefone'        => 'sometimes|required|string',
            'data_nascimento' => 'sometimes|required|date',
            'endereco'        => 'sometimes|required|string',
            'bairro'          => 'sometimes|required|string',
            'cep'             => 'sometimes|required|string',
            'complemento'     => 'nullable|string',
        ]);

        $customer->update($request->all());

        return response()->json($customer);
    }

    /**
     * @OA\Delete(
     *     path="/api/customers/{id}",
     *     summary="Excluir cliente",
     *     tags={"Customers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do cliente",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Cliente excluído com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cliente não encontrado"
     *     )
     * )
     */
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return response()->json(null, 204);
    }
}