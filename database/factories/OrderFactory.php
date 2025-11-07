<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;

class OrderFactory extends Factory
{


     public function withFaker()
    {
        return \Faker\Factory::create('pt_BR');
    }


    public function definition(): array
    {
        return [
            'customers_id' => Customer::factory(),
            
        ];
    }
 

    public function configure()
    {
        return $this->afterCreating(function (Order $order) {
            // Cria de 1 a 3 produtos e associa ao pedido
            $products = Product::factory()->count(rand(1, 4))->create();

            collect($products)->each(function ($product) use ($order) {
                $order->products()->attach($product->id, [
                    'quantidade' => rand(1, 4),
                    'valorCompra' => $product->price ?? fake()->randomFloat(2, 10, 100),
                ]);
            });
        });
    }
}

