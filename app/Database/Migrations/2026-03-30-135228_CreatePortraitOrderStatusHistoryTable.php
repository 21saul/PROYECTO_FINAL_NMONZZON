<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace App\Database\Migrations;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Database\Migration;

// DECLARA UNA CLASE
class CreatePortraitOrderStatusHistoryTable extends Migration
// DELIMITADOR DE BLOQUE
{
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function up()
    // DELIMITADOR DE BLOQUE
    {
        // INSTRUCCIÓN O DECLARACIÓN PHP
        $this->forge->addField([
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'id'                => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'portrait_order_id' => ['type' => 'INT', 'unsigned' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'from_status'       => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'to_status'         => ['type' => 'VARCHAR', 'constraint' => 30],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'changed_by'        => ['type' => 'INT', 'unsigned' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'notes'             => ['type' => 'TEXT', 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->addPrimaryKey('id');
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->addForeignKey('portrait_order_id', 'portrait_orders', 'id', 'CASCADE', 'CASCADE');
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->addForeignKey('changed_by', 'users', 'id', 'CASCADE', 'RESTRICT');
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->createTable('portrait_order_status_history');
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function down()
    // DELIMITADOR DE BLOQUE
    {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->dropTable('portrait_order_status_history');
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
