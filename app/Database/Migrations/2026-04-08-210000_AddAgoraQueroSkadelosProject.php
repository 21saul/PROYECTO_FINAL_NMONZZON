<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Añade el proyecto de portada «Agora Quero — Skadelos» al portfolio de diseño.
 */
class AddAgoraQueroSkadelosProject extends Migration
{
    private const SLUG = 'agora-quero-skadelos';

    public function up(): void
    {
        $slug = self::SLUG;
        $row  = $this->db->query(
            'SELECT id, deleted_at FROM design_projects WHERE slug = ? LIMIT 1',
            [$slug]
        )->getRow();

        if ($row !== null && $row->deleted_at === null) {
            return;
        }

        $title       = 'Agora Quero — Skadelos';
        $description = "Arte de portada para el lanzamiento «Agora Quero» de Skadelos: ilustración en blanco y negro con trazo de tinta y sombreado a trazo, sobre fondo con textura de papel.\n\nComposición centrada en un corazón anatómico, manos, bocina de gramófono y detalles simbólicos; tipografía serif para título y firma del artista. Pieza cuadrada pensada para carátula digital (streaming y redes).";
        $featured    = 'uploads/diseno/diseno-agora-quero-skadelos.png';
        $now         = date('Y-m-d H:i:s');

        $maxRow  = $this->db->query(
            'SELECT COALESCE(MAX(sort_order), 0) AS m FROM design_projects WHERE deleted_at IS NULL'
        )->getRow();
        $maxSort = $maxRow !== null ? (int) $maxRow->m : 0;
        $sortOrder = $maxSort + 1;

        if ($row !== null) {
            $projId = (int) $row->id;
            $this->db->table('design_projects')->where('id', $projId)->update([
                'title'           => $title,
                'description'     => $description,
                'design_type'     => 'musica',
                'featured_image'  => $featured,
                'is_featured'     => 1,
                'is_active'       => 1,
                'sort_order'      => $sortOrder,
                'deleted_at'      => null,
                'updated_at'      => $now,
            ]);
            $this->db->table('design_project_images')->where('design_project_id', $projId)->delete();
        } else {
            $this->db->table('design_projects')->insert([
                'title'          => $title,
                'slug'           => $slug,
                'description'    => $description,
                'design_type'    => 'musica',
                'featured_image' => $featured,
                'is_featured'    => 1,
                'is_active'      => 1,
                'sort_order'     => $sortOrder,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);
            $projId = (int) $this->db->insertID();
        }

        $this->db->table('design_project_images')->insert([
            'design_project_id' => $projId,
            'image_url'         => $featured,
            'alt_text'          => $title . ' - 1',
            'sort_order'        => 1,
            'created_at'        => $now,
        ]);
    }

    public function down(): void
    {
        $row = $this->db->query(
            'SELECT id FROM design_projects WHERE slug = ? AND deleted_at IS NULL LIMIT 1',
            [self::SLUG]
        )->getRow();
        if ($row === null) {
            return;
        }
        $id = (int) $row->id;
        $this->db->table('design_project_images')->where('design_project_id', $id)->delete();
        $this->db->table('design_projects')->where('id', $id)->update([
            'deleted_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
