<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */


      public function withFaker()
        {
            return \Faker\Factory::create('pt_BR');
        }


    public function definition(): array
    {
        return [
            'nome' => $this->faker->word,
            'preco' => $this->faker->randomFloat(2, 1, 100),
            'foto' => 'foto/placeholder.jpg',
            'type' => $this->faker->randomElement(['Pastéis', 'Coxinhas', 'Hambúrgueres', 'Acompanhamentos']),
        ];
    }
}
