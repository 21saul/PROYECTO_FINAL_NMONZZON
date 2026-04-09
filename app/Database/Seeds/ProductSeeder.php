<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace App\Database\Seeds;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Database\Seeder;

// DECLARA UNA CLASE
class ProductSeeder extends Seeder
// DELIMITADOR DE BLOQUE
{
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function run()
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $categoryId = 6;

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $totebags = [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Totebag Erizo', 'slug' => 'totebag-erizo', 'price' => 20.00, 'featured_image' => 'uploads/productos/totebags/totebag-erizo.png', 'short_description' => 'Totebag con ilustracion de erizo'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Totebag Pulpo', 'slug' => 'totebag-pulpo', 'price' => 20.00, 'featured_image' => 'uploads/productos/totebags/totebag-pulpo.png', 'short_description' => 'Totebag con ilustracion de pulpo'],
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ];

        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($totebags as $tb) {
            // INSTRUCCIÓN O DECLARACIÓN PHP
            $this->db->table('products')->insert([
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'category_id' => $categoryId,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'name' => $tb['name'],
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'slug' => $tb['slug'],
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'description' => null,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'short_description' => $tb['short_description'],
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'price' => $tb['price'],
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'stock' => 100,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'featured_image' => $tb['featured_image'],
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'is_active' => 1,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'created_at' => date('Y-m-d H:i:s'),
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'updated_at' => date('Y-m-d H:i:s'),
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ]);
        // DELIMITADOR DE BLOQUE
        }

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $prints = [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Cala linea', 'image' => 'cala-linea.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Sterlizia linea', 'image' => 'sterlizia-linea.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Bufalo', 'image' => 'bufalo.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Mike Wazowski', 'image' => 'mike-wazowski.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Pulpo vertical', 'image' => 'pulpo-vertical.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Paco', 'image' => 'paco.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Chica linea', 'image' => 'chica-linea.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Aretha Franklin', 'image' => 'aretha-franklin.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Rana bici flores', 'image' => 'rana-bici-flores.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Cabeza animales', 'image' => 'cabeza-animales.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Cabeza ola', 'image' => 'cabeza-ola.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Cabeza casa', 'image' => 'cabeza-casa.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Cabeza Galicia', 'image' => 'cabeza-galicia.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'El juego del calamar', 'image' => 'el-juego-del-calamar.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Gallo teriomorfo', 'image' => 'gallo-teriomorfo.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Maquina de coser', 'image' => 'maquina-de-coser.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Amari', 'image' => 'amari.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Gata teriomorfa', 'image' => 'gata-teriomorfa.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Cebra', 'image' => 'cebra.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Libelula', 'image' => 'libelula.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Mariposa', 'image' => 'mariposa.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Maquina coser', 'image' => 'maquina-coser.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => '3 mariposas', 'image' => '3-mariposas.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Lince', 'image' => 'lince.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Willow Smith', 'image' => 'willow-smith.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Kamali', 'image' => 'kamali.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Quino Mafalda', 'image' => 'quino-mafalda.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Pajaro teriomorfo', 'image' => 'pajaro-teriomorfo.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Angela Molina', 'image' => 'angela-molina.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Gato teriomorfo', 'image' => 'gato-teriomorfo.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Joker', 'image' => 'joker.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Nala', 'image' => 'nala.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Telefono antiguo', 'image' => 'telefono-antiguo.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Dani Rovira', 'image' => 'dani-rovira.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Mariquitilla', 'image' => 'mariquitilla.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Erizo', 'image' => 'erizo.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Lagartija', 'image' => 'lagartija.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Rana biciclo', 'image' => 'rana-biciclo.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Samurai', 'image' => 'samurai.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Cangrejos', 'image' => 'cangrejos.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Freddie', 'image' => 'freddie.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Rana bici', 'image' => 'rana-bici.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Cafe molinillo', 'image' => 'cafe-molinillo.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Sansa y Arya', 'image' => 'sansa-y-arya.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Zorro cama', 'image' => 'zorro-cama.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Padre e hijo', 'image' => 'padre-e-hijo.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Tony Stark', 'image' => 'tony-stark.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Chica libro', 'image' => 'chica-libro.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Camaleon silla', 'image' => 'camaleon-silla.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Chica planta', 'image' => 'chica-planta.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Batman', 'image' => 'batman.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'The last of us', 'image' => 'the-last-of-us.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Thoros y Dondarrion', 'image' => 'thoros-y-dondarrion.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Mantis religiosa', 'image' => 'mantis-religiosa.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Tortuga', 'image' => 'tortuga.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Tigre', 'image' => 'tigre.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Caballo en linea', 'image' => 'caballo-en-linea.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Murcielagos', 'image' => 'murcielagos.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Tortuga 2', 'image' => 'tortuga-2.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Ojos aguila', 'image' => 'ojos-aguila.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Escorpion', 'image' => 'escorpion.jpg'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Pulpo', 'image' => 'pulpo.jpg'],
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ];

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $description = 'Lamina impresa en papel de acuarela de 300 gr. Marco no incluido.';

        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($prints as $print) {
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $slug = strtolower(str_replace([' ', 'á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü'], ['-', 'a', 'e', 'i', 'o', 'u', 'n', 'u'], $print['name']));
            // CONDICIONAL SI
            if ($this->db->table('products')->where('slug', $slug)->countAllResults() > 0) {
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                $slug .= '-2';
            // DELIMITADOR DE BLOQUE
            }

            // INSTRUCCIÓN O DECLARACIÓN PHP
            $this->db->table('products')->insert([
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'category_id' => $categoryId,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'name' => $print['name'],
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'slug' => $slug,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'description' => $description,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'short_description' => $description,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'price' => 15.00,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'stock' => 100,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'featured_image' => 'uploads/productos/prints/' . $print['image'],
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'is_active' => 1,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'created_at' => date('Y-m-d H:i:s'),
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'updated_at' => date('Y-m-d H:i:s'),
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ]);

            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $productId = $this->db->insertID();

            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $variants = [
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                ['variant_name' => 'Tamano', 'variant_value' => 'A5', 'price_modifier' => 0.00, 'stock' => 100],
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                ['variant_name' => 'Tamano', 'variant_value' => 'A4', 'price_modifier' => 5.00, 'stock' => 100],
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                ['variant_name' => 'Tamano', 'variant_value' => 'A3', 'price_modifier' => 10.00, 'stock' => 100],
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ];

            // BUCLE FOREACH SOBRE COLECCIÓN
            foreach ($variants as $variant) {
                // INSTRUCCIÓN O DECLARACIÓN PHP
                $this->db->table('product_variants')->insert([
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'product_id' => $productId,
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'variant_name' => $variant['variant_name'],
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'variant_value' => $variant['variant_value'],
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'price_modifier' => $variant['price_modifier'],
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'stock' => $variant['stock'],
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'is_active' => 1,
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'created_at' => date('Y-m-d H:i:s'),
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'updated_at' => date('Y-m-d H:i:s'),
                // INSTRUCCIÓN O DECLARACIÓN PHP
                ]);
            // DELIMITADOR DE BLOQUE
            }
        // DELIMITADOR DE BLOQUE
        }
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
