<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Orquesta seeds de tienda (prints/totebags desde disco).
 * Uso: php spark db:seed DatabaseSeeder
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(ProductCategorySeeder::class);
        $this->call(ProductImagesSeeder::class);
    }
}
