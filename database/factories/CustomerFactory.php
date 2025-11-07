<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{

     public function withFaker()
        {
            return \Faker\Factory::create('pt_BR');
        }
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'telefone' => $this->faker->phoneNumber,
            'data_nascimento' => $this->faker->dateTimeBetween('-30 years', '-18 years')->format('Y-m-d'),
            'endereco' => $this->faker->streetAddress,
            'bairro' =>  $this->faker->citySuffix,
            'cep' => $this->faker->postcode,
        ];
    }
}
