<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace App\Database\Migrations;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Database\Migration;

// DECLARA UNA CLASE
class CreateUsersTable extends Migration
// DELIMITADOR DE BLOQUE
{
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function up()
    // DELIMITADOR DE BLOQUE
    {
        // INSTRUCCIÓN O DECLARACIÓN PHP
        $this->forge->addField([
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'id'              => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'uuid'            => ['type' => 'CHAR', 'constraint' => 36, 'unique' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'name'            => ['type' => 'VARCHAR', 'constraint' => 100],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'email'           => ['type' => 'VARCHAR', 'constraint' => 255, 'unique' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'password'        => ['type' => 'VARCHAR', 'constraint' => 255],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'role'            => ['type' => 'ENUM', 'constraint' => ['admin', 'client'], 'default' => 'client'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'phone'           => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'avatar'          => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'email_verified_at' => ['type' => 'DATETIME', 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'remember_token'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'is_active'       => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'last_login_at'   => ['type' => 'DATETIME', 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'last_login_ip'   => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'failed_login_attempts' => ['type' => 'INT', 'default' => 0],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'locked_until'    => ['type' => 'DATETIME', 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'deleted_at'      => ['type' => 'DATETIME', 'null' => true],
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->addPrimaryKey('id');
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->addKey('role');
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->createTable('users');
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function down()
    // DELIMITADOR DE BLOQUE
    {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->dropTable('users');
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
