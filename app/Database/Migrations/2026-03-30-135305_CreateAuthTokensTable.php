<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace App\Database\Migrations;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Database\Migration;

// DECLARA UNA CLASE
class CreateAuthTokensTable extends Migration
// DELIMITADOR DE BLOQUE
{
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function up()
    // DELIMITADOR DE BLOQUE
    {
        // INSTRUCCIÓN O DECLARACIÓN PHP
        $this->forge->addField([
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'user_id'       => ['type' => 'INT', 'unsigned' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'token_hash'    => ['type' => 'VARCHAR', 'constraint' => 255],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'expires_at'    => ['type' => 'DATETIME'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'ip_address'    => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'user_agent'    => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'is_revoked'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->addPrimaryKey('id');
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->addKey('token_hash');
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->createTable('auth_tokens');
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function down()
    // DELIMITADOR DE BLOQUE
    {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->dropTable('auth_tokens');
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
