<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return Product::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string',
            'price' => 'required|numeric',
            'photo' => 'required|string', // URL or filename
        ]);

        $product = Product::create($request->all());

        return response()->json($product, 201);
    }

    public function show($id)
    {
        return Product::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name'  => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric',
            'photo' => 'sometimes|required|string',
        ]);

        $product->update($request->all());

        return response()->json($product);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(null, 204);
    }
}
