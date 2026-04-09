<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace App\Database\Migrations;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Database\Migration;

// DECLARA UNA CLASE
class CreateSiteSettingsTable extends Migration
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
            'key'         => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'value'       => ['type' => 'TEXT', 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'type'        => ['type' => 'ENUM', 'constraint' => ['text','textarea','image','boolean','json'], 'default' => 'text'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'group'       => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'general'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->addPrimaryKey('id');
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->createTable('site_settings');
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function down()
    // DELIMITADOR DE BLOQUE
    {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->dropTable('site_settings');
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
