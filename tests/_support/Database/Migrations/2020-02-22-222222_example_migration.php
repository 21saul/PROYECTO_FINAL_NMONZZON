<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace Tests\Support\Database\Migrations;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Database\Migration;

// DECLARA UNA CLASE
class ExampleMigration extends Migration
// DELIMITADOR DE BLOQUE
{
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    protected $DBGroup = 'tests';

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function up(): void
    // DELIMITADOR DE BLOQUE
    {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->addField('id');
        // INSTRUCCIÓN O DECLARACIÓN PHP
        $this->forge->addField([
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'name'       => ['type' => 'varchar', 'constraint' => 31],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'uid'        => ['type' => 'varchar', 'constraint' => 31],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'class'      => ['type' => 'varchar', 'constraint' => 63],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'icon'       => ['type' => 'varchar', 'constraint' => 31],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'summary'    => ['type' => 'varchar', 'constraint' => 255],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'created_at' => ['type' => 'datetime', 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'updated_at' => ['type' => 'datetime', 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'deleted_at' => ['type' => 'datetime', 'null' => true],
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]);

        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->addKey('name');
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->addKey('uid');
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->addKey(['deleted_at', 'id']);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->addKey('created_at');

        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->createTable('factories');
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function down(): void
    // DELIMITADOR DE BLOQUE
    {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->dropTable('factories');
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
