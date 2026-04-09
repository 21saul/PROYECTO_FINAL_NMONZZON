<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace App\Database\Seeds;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Database\Seeder;

// DECLARA UNA CLASE
class AdminUserSeeder extends Seeder
// DELIMITADOR DE BLOQUE
{
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function run()
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $data = [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'uuid' => bin2hex(random_bytes(18)),
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'name' => 'nmonzzon Admin',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'email' => 'admin@nmonzzon.com',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'password' => password_hash('NmzAdmin2026!', PASSWORD_ARGON2ID),
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'role' => 'admin',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'is_active' => 1,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'email_verified_at' => date('Y-m-d H:i:s'),
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'created_at' => date('Y-m-d H:i:s'),
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'updated_at' => date('Y-m-d H:i:s'),
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ];

        // EVITA ERROR POR EMAIL DUPLICADO SI EL SEEDER SE EJECUTA MÁS DE UNA VEZ
        $already = $this->db->table('users')->where('email', $data['email'])->countAllResults();
        if ($already > 0) {
            return;
        }

        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->db->table('users')->insert($data);
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
