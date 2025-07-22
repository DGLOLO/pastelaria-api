<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Customer;
use App\Models\Product;
use App\Mail\OrderConfirmation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

class OrderEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_is_sent_when_order_is_created()
    {
        Mail::fake();

        $customer = Customer::factory()->create();
        $product = Product::factory()->create();

        $response = $this->postJson('/api/orders', [
            'customer_id' => $customer->id,
            'products' => [$product->id]
        ]);

        $response->assertStatus(201);

        Mail::assertSent(OrderConfirmation::class, function ($mail) use ($customer) {
            return $mail->hasTo($customer->email);
        });
    }

    public function test_email_contains_order_details()
    {
        Mail::fake();

        $customer = Customer::factory()->create();
        $product = Product::factory()->create([
            'nome' => 'Pastel de Carne',
            'preco' => 8.50,
            'foto' => 'https://example.com/pastel.jpg'
        ]);

        $response = $this->postJson('/api/orders', [
            'customer_id' => $customer->id,
            'products' => [$product->id]
        ]);

        $order = $response->json();

        Mail::assertSent(OrderConfirmation::class, function ($mail) use ($order, $customer, $product) {
            return $mail->order->id === $order['id'] &&
                   $mail->order->customer->id === $customer->id &&
                   $mail->order->products->first()->id === $product->id;
        });
    }

    public function test_email_subject_contains_order_number()
    {
        Mail::fake();

        $customer = Customer::factory()->create();
        $product = Product::factory()->create();

        $response = $this->postJson('/api/orders', [
            'customer_id' => $customer->id,
            'products' => [$product->id]
        ]);

        $order = $response->json();

        Mail::assertSent(OrderConfirmation::class, function ($mail) use ($order) {
            return $mail->order->id === $order['id'];
        });
    }
}
