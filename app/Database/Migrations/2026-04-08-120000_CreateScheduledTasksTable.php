<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateScheduledTasksTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'task_type'     => ['type' => 'VARCHAR', 'constraint' => 64],
            'reference_id'  => ['type' => 'INT', 'unsigned' => true],
            'run_at'        => ['type' => 'DATETIME'],
            'processed_at'  => ['type' => 'DATETIME', 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('run_at');
        $this->forge->addKey(['task_type', 'reference_id'], false, true);
        $this->forge->createTable('scheduled_tasks');
    }

    public function down(): void
    {
        $this->forge->dropTable('scheduled_tasks');
    }
}
