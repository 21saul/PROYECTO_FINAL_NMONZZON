<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Añade el proyecto «Diseño textil — ropa y merchandising» como tercer card,
 * sin modificar «Disenos digitales» ni el libro Apalpador.
 */
class AddDisenoTextilRopaProject extends Migration
{
    private const SLUG = 'diseno-textil-ropa';

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

        $title       = 'Diseño textil — ropa y merchandising';
        $description = "Diseño gráfico aplicado a ropa y complementos: ilustración para camiseta (motivo reloj de arena), mockup en perchero y tote bag con ilustración tipo tinta.\n\nArchivos pensados para imprenta textil o proveedor, con línea gráfica unificada sobre tejidos claros.";
        $featured    = 'uploads/diseno/diseno-textil-camiseta-modelo.png';
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
                'design_type'     => 'textil',
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
                'design_type'    => 'textil',
                'featured_image' => $featured,
                'is_featured'    => 1,
                'is_active'      => 1,
                'sort_order'     => $sortOrder,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);
            $projId = (int) $this->db->insertID();
        }

        $images = [
            'uploads/diseno/diseno-textil-camiseta-modelo.png',
            'uploads/diseno/diseno-textil-camiseta-mockup.png',
            'uploads/diseno/diseno-textil-tote-erizo.png',
        ];
        $sortImg = 1;
        foreach ($images as $url) {
            $this->db->table('design_project_images')->insert([
                'design_project_id' => $projId,
                'image_url'         => $url,
                'alt_text'          => $title . ' - ' . $sortImg,
                'sort_order'        => $sortImg,
                'created_at'        => $now,
            ]);
            $sortImg++;
        }
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
