<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterPortraitOrdersReferencePhotoToText extends Migration
{
    public function up(): void
    {
        $this->forge->modifyColumn('portrait_orders', [
            'reference_photo' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->modifyColumn('portrait_orders', [
            'reference_photo' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
            ],
        ]);
    }
}
