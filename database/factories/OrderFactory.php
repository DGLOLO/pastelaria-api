<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Customer;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'customers_id' => Customer::factory(),
            'total' => $this->faker->randomFloat(2, 10, 500), 
        ];
    }
}

