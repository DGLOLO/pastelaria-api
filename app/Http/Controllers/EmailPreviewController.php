<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Mail\OrderConfirmation;
use Illuminate\Http\Request;

class EmailPreviewController extends Controller
{
    public function index()
    {
        $orders = Order::with(['customer', 'products'])->latest()->take(10)->get();
        return view('email-preview-index', compact('orders'));
    }

    public function preview(Request $request)
    {
        $orderId = $request->get('order_id');
        
        // Se um order_id foi fornecido, buscar esse pedido específico
        if ($orderId) {
            $order = Order::with(['customer', 'products'])->find($orderId);
            
            if ($order) {
                return view('emails.order-confirmation', compact('order'));
            }
        }

        // Se não foi fornecido order_id ou não encontrou, criar dados fictícios
        $customer = Customer::first() ?? Customer::create([
            'nome' => 'Allan Cerquera',
            'email' => 'Allan@exemplo.com',
            'telefone' => '11999999999',
            'data_nascimento' => '1990-01-01',
            'endereco' => 'Rua das Flores, 123',
            'bairro' => 'Centro',
            'cep' => '01234-567',
            'complemento' => 'Apto 101'
        ]);

        // Criar pedido fictício
        $order = Order::create([
            'customers_id' => $customer->id
        ]);

        // Adicionar produtos ao pedido
        $products = Product::take(3)->get();
        if ($products->count() > 0) {
            $order->products()->attach($products->pluck('id'));
        }

        // Recarregar relacionamentos
        $order->load(['customer', 'products']);

        // Retornar a view do email
        return view('emails.order-confirmation', compact('order'));
    }

    public function previewOrder($orderId)
    {
        $order = Order::with(['customer', 'products'])->find($orderId);
        
        if (!$order) {
            return redirect()->route('email.preview.index')->with('error', 'Pedido não encontrado');
        }

        return view('emails.order-confirmation', compact('order'));
    }
}
