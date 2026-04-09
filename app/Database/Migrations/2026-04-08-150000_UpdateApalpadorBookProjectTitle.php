<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateApalpadorBookProjectTitle extends Migration
{
    public function up(): void
    {
        $this->db->table('design_projects')
            ->where('slug', 'libro-apalpador-editorial')
            ->update([
                'title'      => 'Libro Apalpador — diseño editorial',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
    }

    public function down(): void
    {
        $this->db->table('design_projects')
            ->where('slug', 'libro-apalpador-editorial')
            ->update([
                'title'      => 'Libro Apalpador - Diseno Editorial',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
    }
}
