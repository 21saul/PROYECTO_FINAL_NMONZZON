<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace App\Database\Migrations;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Database\Migration;

// DECLARA UNA CLASE
class CreateDesignProjectImagesTable extends Migration
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
            'design_project_id' => ['type' => 'INT', 'unsigned' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'image_url'         => ['type' => 'VARCHAR', 'constraint' => 500],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'alt_text'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'sort_order'        => ['type' => 'INT', 'default' => 0],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->addPrimaryKey('id');
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->addForeignKey('design_project_id', 'design_projects', 'id', 'CASCADE', 'CASCADE');
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->createTable('design_project_images');
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function down()
    // DELIMITADOR DE BLOQUE
    {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->dropTable('design_project_images');
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
