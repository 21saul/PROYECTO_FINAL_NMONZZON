<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace App\Database\Migrations;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Database\Migration;

// DECLARA UNA CLASE
class CreateContactMessagesTable extends Migration
// DELIMITADOR DE BLOQUE
{
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function up()
    // DELIMITADOR DE BLOQUE
    {
        // INSTRUCCIÓN O DECLARACIÓN PHP
        $this->forge->addField([
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'name'        => ['type' => 'VARCHAR', 'constraint' => 100],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'email'       => ['type' => 'VARCHAR', 'constraint' => 255],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'phone'       => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'subject'     => ['type' => 'VARCHAR', 'constraint' => 200],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'message'     => ['type' => 'TEXT'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'category'    => ['type' => 'ENUM', 'constraint' => ['general','portrait','live_art','branding','design','products','other'], 'default' => 'general'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'is_read'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'is_replied'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'replied_at'  => ['type' => 'DATETIME', 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'ip_address'  => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->addPrimaryKey('id');
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->createTable('contact_messages');
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function down()
    // DELIMITADOR DE BLOQUE
    {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->dropTable('contact_messages');
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
