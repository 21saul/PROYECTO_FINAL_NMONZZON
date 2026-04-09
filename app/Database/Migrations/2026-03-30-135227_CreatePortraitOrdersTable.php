<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace App\Database\Migrations;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Database\Migration;

// DECLARA UNA CLASE
class CreatePortraitOrdersTable extends Migration
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
            'order_number'      => ['type' => 'VARCHAR', 'constraint' => 30, 'unique' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'user_id'           => ['type' => 'INT', 'unsigned' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'portrait_style_id' => ['type' => 'INT', 'unsigned' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'portrait_size_id'  => ['type' => 'INT', 'unsigned' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'num_figures'       => ['type' => 'INT', 'default' => 1],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'with_frame'        => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'frame_type'        => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'status'            => ['type' => 'ENUM', 'constraint' => ['quote','accepted','photo_received','in_progress','revision','delivered','completed','cancelled'], 'default' => 'quote'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'base_price'        => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'extras_price'      => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'total_price'       => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'reference_photo'   => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'sketch_image'      => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'final_image'       => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'client_notes'      => ['type' => 'TEXT', 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'admin_notes'       => ['type' => 'TEXT', 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'stripe_payment_id' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'payment_status'    => ['type' => 'ENUM', 'constraint' => ['pending','paid','refunded','failed'], 'default' => 'pending'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'invoice_path'      => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'paid_at'           => ['type' => 'DATETIME', 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'delivered_at'      => ['type' => 'DATETIME', 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'deleted_at'        => ['type' => 'DATETIME', 'null' => true],
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->addPrimaryKey('id');
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'RESTRICT');
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->addForeignKey('portrait_style_id', 'portrait_styles', 'id', 'CASCADE', 'RESTRICT');
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->addForeignKey('portrait_size_id', 'portrait_sizes', 'id', 'CASCADE', 'RESTRICT');
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->addKey('status');
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->addKey('payment_status');
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->createTable('portrait_orders');
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function down()
    // DELIMITADOR DE BLOQUE
    {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->dropTable('portrait_orders');
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
