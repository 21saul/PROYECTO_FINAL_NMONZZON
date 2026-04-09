<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace App\Database\Seeds;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Database\Seeder;

// DECLARA UNA CLASE
class PortfolioWorkSeeder extends Seeder
// DELIMITADOR DE BLOQUE
{
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function run()
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $retratosId = 1;
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $productosId = 6;

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $numberedImages = [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            1, 2, 3, 4, 5, 6, 7, 8, 9, 10,
            // INSTRUCCIÓN O DECLARACIÓN PHP
            11, 12, 13, 14, 15, 16, 17, 18, 19, 20,
            // INSTRUCCIÓN O DECLARACIÓN PHP
            21, 22, 23, 24, 25, 26, 27, 28, 29, 31,
            // INSTRUCCIÓN O DECLARACIÓN PHP
            32, 33, 34, 35, 36, 37, 38, 39, 40, 42,
            // INSTRUCCIÓN O DECLARACIÓN PHP
            43, 44, 52, 58, 59, 60, 61, 63, 64, 65,
            // INSTRUCCIÓN O DECLARACIÓN PHP
            66, 67, 68, 69, 70, 71, 72, 73, 74, 75,
            // INSTRUCCIÓN O DECLARACIÓN PHP
            76, 77, 78, 79,
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ];

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $order = 1;
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($numberedImages as $num) {
            // INSTRUCCIÓN O DECLARACIÓN PHP
            $this->db->table('portfolio_works')->insert([
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'category_id' => $productosId,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'title'       => 'Obra #' . $num,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'slug'        => 'obra-' . $num,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'image_url'   => 'portfolio/' . $num . '.jpg',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'is_active'   => 1,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'sort_order'  => $order++,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'created_at'  => date('Y-m-d H:i:s'),
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'updated_at'  => date('Y-m-d H:i:s'),
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ]);
        // DELIMITADOR DE BLOQUE
        }

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $clientPortraits = [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'Alba_Méndez.jpg',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'Andrea_Figueiras_.jpg',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'Andrea_Mara.jpg',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'Bea_prima_Raqui.jpg',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'Bianca_Santana.png',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'Elisa_Goris.jpg',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'Emilio.jpg',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'Familia_Álvarez.jpg',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'Gon.png',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'Kairos.png',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'Lara_y_Marco.jpg',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'Lucía_Cedeira_Durán.jpg',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'Lucía_Picallo.jpg',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'Lucía_Álvarez.png',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'Martu.jpg',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'Pablo_Garrido.png',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'Sandra_Maceira.png',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'Sara_Álvarez.jpg',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'Wilson.jpg',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'Xoana_.jpg',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'Yoli_Rodríguez_.jpg',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'Álvaro_papi_Ésqui.jpg',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'Agapito.png',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ];

        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($clientPortraits as $portrait) {
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $name = pathinfo($portrait, PATHINFO_FILENAME);
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $name = str_replace('_', ' ', $name);
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $name = trim($name);
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', iconv('UTF-8', 'ASCII//TRANSLIT', $name)));
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $slug = trim($slug, '-');

            // INSTRUCCIÓN O DECLARACIÓN PHP
            $this->db->table('portfolio_works')->insert([
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'category_id' => $retratosId,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'title'       => 'Retrato - ' . $name,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'slug'        => 'retrato-' . $slug,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'image_url'   => 'retratos/clientes/' . $portrait,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'style_tag'   => 'retrato',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'is_active'   => 1,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'sort_order'  => $order++,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'created_at'  => date('Y-m-d H:i:s'),
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'updated_at'  => date('Y-m-d H:i:s'),
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ]);
        // DELIMITADOR DE BLOQUE
        }
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
