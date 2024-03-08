<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Producto;

class ProductosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Producto::create([
            'nombre' => 'Democracia',
            'descripcion' => 'Compra un voto extra para un campeón sugerido',
            'precio' => 300,
        ]);

        Producto::create([
            'nombre' => 'Anarquía',
            'descripcion' => 'Quitale un voto a un campeón sugerido',
            'precio' => 250,
        ]);

        Producto::create([
            'nombre' => 'Cállese alv',
            'descripcion' => 'Silencia un miembro (incluso al capibe) durante una hora',
            'precio' => 1000,
        ]);
        
    }
}