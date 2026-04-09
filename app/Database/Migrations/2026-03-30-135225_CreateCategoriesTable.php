<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace App\Database\Migrations;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Database\Migration;

// DECLARA UNA CLASE
class CreateCategoriesTable extends Migration
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
            'slug'        => ['type' => 'VARCHAR', 'constraint' => 120, 'unique' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'description' => ['type' => 'TEXT', 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'image'       => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'icon'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'sort_order'  => ['type' => 'INT', 'default' => 0],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'is_active'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'meta_title'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'meta_description' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'deleted_at'  => ['type' => 'DATETIME', 'null' => true],
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->addPrimaryKey('id');
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->createTable('categories');
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function down()
    // DELIMITADOR DE BLOQUE
    {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->dropTable('categories');
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
