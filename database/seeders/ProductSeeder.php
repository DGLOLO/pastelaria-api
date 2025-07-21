<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'nome' => 'Pastel de Carne',
                'preco' => 8.50,
                'foto' => 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?w=400&h=400&fit=crop'
            ],
            [
                'nome' => 'Pastel de Queijo',
                'preco' => 7.50,
                'foto' => 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=400&h=400&fit=crop'
            ],
            [
                'nome' => 'Pastel de Frango',
                'preco' => 8.00,
                'foto' => 'https://images.unsplash.com/photo-1567620905732-2d1ec7ab7445?w=400&h=400&fit=crop'
            ],
            [
                'nome' => 'Pastel de Palmito',
                'preco' => 9.00,
                'foto' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&h=400&fit=crop'
            ],
            [
                'nome' => 'Pastel de Calabresa',
                'preco' => 8.50,
                'foto' => 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=400&h=400&fit=crop'
            ],
            [
                'nome' => 'Pastel de Camarão',
                'preco' => 12.00,
                'foto' => 'https://images.unsplash.com/photo-1567620905732-2d1ec7ab7445?w=400&h=400&fit=crop'
            ],
            [
                'nome' => 'Pastel de Bacalhau',
                'preco' => 11.50,
                'foto' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&h=400&fit=crop'
            ],
            [
                'nome' => 'Pastel de Chocolate',
                'preco' => 6.50,
                'foto' => 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?w=400&h=400&fit=crop'
            ],
            [
                'nome' => 'Pastel de Banana',
                'preco' => 6.00,
                'foto' => 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=400&h=400&fit=crop'
            ],
            [
                'nome' => 'Pastel de Doce de Leite',
                'preco' => 6.50,
                'foto' => 'https://images.unsplash.com/photo-1567620905732-2d1ec7ab7445?w=400&h=400&fit=crop'
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
