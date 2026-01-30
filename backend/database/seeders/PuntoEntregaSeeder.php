<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PuntoEntrega;

class PuntoEntregaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void {
        PuntoEntrega::factory(12)->create();
    }
}
