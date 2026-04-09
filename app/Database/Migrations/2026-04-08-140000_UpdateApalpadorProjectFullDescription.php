<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateApalpadorProjectFullDescription extends Migration
{
    public function up(): void
    {
        $description = "Diseño e ilustración del libro infantil «El Apalpador», un proyecto editorial para la Aldea do Apalpador: maquetación de interiores, portada e ilustraciones, con archivos preparados para imprenta y una línea gráfica unificada.\n\nHistoria (resumida): el Apalpador es un personaje del imaginario infantil vinculado a la cultura gallega y al entorno rural; el libro lo presenta con un lenguaje cercano para familias y escuelas, y una narrativa visual que acompaña la lectura de principio a fin.";

        $this->db->table('design_projects')
            ->where('slug', 'libro-apalpador-editorial')
            ->update([
                'description' => $description,
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
    }

    public function down(): void
    {
        $this->db->table('design_projects')
            ->where('slug', 'libro-apalpador-editorial')
            ->update([
                'description' => 'Diseno e ilustracion del libro infantil del Apalpador. Maquetacion, ilustraciones interiores y portada.',
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
    }
}
