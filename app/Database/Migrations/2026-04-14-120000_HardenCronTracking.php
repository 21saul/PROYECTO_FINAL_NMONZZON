<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class HardenCronTracking extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('users', [
            'loyalty_last_sent_at' => ['type' => 'DATETIME', 'null' => true, 'after' => 'locked_until'],
            'loyalty_last_activity_at' => ['type' => 'DATETIME', 'null' => true, 'after' => 'loyalty_last_sent_at'],
        ]);

        $this->forge->addColumn('scheduled_tasks', [
            'attempts' => ['type' => 'INT', 'default' => 0, 'after' => 'run_at'],
            'max_attempts' => ['type' => 'INT', 'default' => 5, 'after' => 'attempts'],
            'last_error' => ['type' => 'TEXT', 'null' => true, 'after' => 'max_attempts'],
            'failed_at' => ['type' => 'DATETIME', 'null' => true, 'after' => 'processed_at'],
        ]);
        $this->db->query('CREATE INDEX scheduled_tasks_failed_at_idx ON scheduled_tasks (failed_at)');
    }

    public function down(): void
    {
        $this->forge->dropColumn('users', ['loyalty_last_activity_at', 'loyalty_last_sent_at']);
        $this->db->query('DROP INDEX scheduled_tasks_failed_at_idx ON scheduled_tasks');
        $this->forge->dropColumn('scheduled_tasks', ['failed_at', 'last_error', 'max_attempts', 'attempts']);
    }
}
