<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace App\Database\Migrations;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Database\Migration;

// DECLARA UNA CLASE
class CreateOrdersTable extends Migration
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
            'status'            => ['type' => 'ENUM', 'constraint' => ['pending','processing','shipped','delivered','cancelled','refunded'], 'default' => 'pending'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'subtotal'          => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'shipping_cost'     => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'tax'               => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'discount'          => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'total'             => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'coupon_code'       => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'shipping_name'     => ['type' => 'VARCHAR', 'constraint' => 100],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'shipping_address'  => ['type' => 'VARCHAR', 'constraint' => 500],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'shipping_city'     => ['type' => 'VARCHAR', 'constraint' => 100],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'shipping_postal_code' => ['type' => 'VARCHAR', 'constraint' => 10],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'shipping_country'  => ['type' => 'VARCHAR', 'constraint' => 100, 'default' => 'España'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'shipping_phone'    => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'tracking_number'   => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'stripe_payment_id' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'payment_status'    => ['type' => 'ENUM', 'constraint' => ['pending','paid','refunded','failed'], 'default' => 'pending'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'invoice_path'      => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'notes'             => ['type' => 'TEXT', 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'paid_at'           => ['type' => 'DATETIME', 'null' => true],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'shipped_at'        => ['type' => 'DATETIME', 'null' => true],
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
        $this->forge->addKey('status');
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->createTable('orders');
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function down()
    // DELIMITADOR DE BLOQUE
    {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->forge->dropTable('orders');
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
