<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Retira «Disenos digitales - Varios» del portfolio público (listado y ficha).
 */
class SoftDeleteDisenosDigitalesVarios extends Migration
{
    private const SLUG = 'disenos-digitales-varios';

    public function up(): void
    {
        $now = date('Y-m-d H:i:s');
        $this->db->table('design_projects')
            ->where('slug', self::SLUG)
            ->where('deleted_at', null)
            ->update([
                'is_active'  => 0,
                'deleted_at' => $now,
                'updated_at' => $now,
            ]);
    }

    public function down(): void
    {
        $now = date('Y-m-d H:i:s');
        $this->db->table('design_projects')
            ->where('slug', self::SLUG)
            ->update([
                'is_active'  => 1,
                'deleted_at' => null,
                'updated_at' => $now,
            ]);
    }
}
