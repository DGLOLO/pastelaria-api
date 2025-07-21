<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function index()
    {
        return Order::with(['customer', 'products'])->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'products' => 'required|array',
            'products.*' => 'exists:products,id',
        ]);

        $order = Order::create([
            'customer_id' => $request->customer_id,
        ]);

        $order->products()->attach($request->products);

        // Send simple email notification
        $customer = $order->customer;
        Mail::raw('Order created successfully!', function ($message) use ($customer) {
            $message->to($customer->email)
                    ->subject('Order Confirmation');
        });

        return response()->json($order->load('products'), 201);
    }

    public function show($id)
    {
        return Order::with(['products', 'customer'])->findOrFail($id);
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return response()->json(null, 204);
    }
}
