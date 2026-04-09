<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace App\Database\Migrations;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Database\Migration;

// DECLARA UNA CLASE
class CreatePortraitSizesTable extends Migration
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
            'name'            => ['type' => 'VARCHAR', 'constraint' => 50],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'dimensions'      => ['type' => 'VARCHAR', 'constraint' => 50],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'price_modifier'  => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'sort_order'      => ['type' => 'INT', 'default' => 0],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'is_active'       => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->addPrimaryKey('id');
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->createTable('portrait_sizes');
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function down()
    // DELIMITADOR DE BLOQUE
    {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->dropTable('portrait_sizes');
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
