<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace App\Database\Seeds;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Database\Seeder;

// DECLARA UNA CLASE
class TestimonialSeeder extends Seeder
// DELIMITADOR DE BLOQUE
{
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function run()
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $data = [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            [
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'client_name' => 'Alba Méndez',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'service_type' => 'Retrato personalizado',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'rating' => 5,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'content' => 'Increíble el trabajo de Noelia. El retrato quedó precioso, con un nivel de detalle impresionante. Súper recomendable.',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'is_featured' => 1,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'is_active' => 1,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'sort_order' => 1,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'created_at' => date('Y-m-d H:i:s'),
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'updated_at' => date('Y-m-d H:i:s'),
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ],
            // INSTRUCCIÓN O DECLARACIÓN PHP
            [
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'client_name' => 'Sandra Maceira',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'service_type' => 'Retrato personalizado',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'rating' => 5,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'content' => 'Un regalo perfecto. La artista captó cada detalle y la calidad del papel es excelente. Volvería a encargar sin dudarlo.',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'is_featured' => 1,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'is_active' => 1,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'sort_order' => 2,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'created_at' => date('Y-m-d H:i:s'),
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'updated_at' => date('Y-m-d H:i:s'),
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ],
            // INSTRUCCIÓN O DECLARACIÓN PHP
            [
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'client_name' => 'Pablo Garrido',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'service_type' => 'Retrato en blanco y negro',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'rating' => 5,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'content' => 'Quedé encantado con mi retrato. El estilo en blanco y negro es espectacular. Gran artista y muy profesional.',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'is_featured' => 1,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'is_active' => 1,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'sort_order' => 3,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'created_at' => date('Y-m-d H:i:s'),
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'updated_at' => date('Y-m-d H:i:s'),
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ],
            // INSTRUCCIÓN O DECLARACIÓN PHP
            [
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'client_name' => 'Lucía Álvarez',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'service_type' => 'Arte en vivo - Boda',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'rating' => 5,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'content' => 'Contratamos a Noelia para nuestra boda y fue un éxito total. Los invitados quedaron encantados con sus retratos en vivo.',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'is_featured' => 1,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'is_active' => 1,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'sort_order' => 4,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'created_at' => date('Y-m-d H:i:s'),
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'updated_at' => date('Y-m-d H:i:s'),
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ],
            // INSTRUCCIÓN O DECLARACIÓN PHP
            [
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'client_name' => 'Familia Álvarez',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'service_type' => 'Retrato familiar',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'rating' => 5,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'content' => 'Encargamos un retrato familiar y el resultado superó todas nuestras expectativas. Un recuerdo para toda la vida.',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'is_featured' => 0,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'is_active' => 1,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'sort_order' => 5,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'created_at' => date('Y-m-d H:i:s'),
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'updated_at' => date('Y-m-d H:i:s'),
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ],
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ];

        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->db->table('testimonials')->insertBatch($data);
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
