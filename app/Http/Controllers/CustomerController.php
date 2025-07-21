<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        return Customer::all();
    }

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

    public function show($id)
    {
        return Customer::findOrFail($id);
    }

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

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return response()->json(null, 204);
    }
}
