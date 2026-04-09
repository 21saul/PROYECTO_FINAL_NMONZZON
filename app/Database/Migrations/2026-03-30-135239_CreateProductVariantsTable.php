<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace App\Database\Migrations;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Database\Migration;

// DECLARA UNA CLASE
class CreateProductVariantsTable extends Migration
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
            'product_id'      => ['type' => 'INT', 'unsigned' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'variant_name'    => ['type' => 'VARCHAR', 'constraint' => 100],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'variant_value'   => ['type' => 'VARCHAR', 'constraint' => 100],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'price_modifier'  => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'stock'           => ['type' => 'INT', 'default' => 0],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'sku'             => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
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
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->createTable('product_variants');
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function down()
    // DELIMITADOR DE BLOQUE
    {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->dropTable('product_variants');
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
