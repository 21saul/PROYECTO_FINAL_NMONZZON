<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace App\Database\Seeds;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Database\Seeder;

// DECLARA UNA CLASE
class SiteSettingsSeeder extends Seeder
// DELIMITADOR DE BLOQUE
{
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function run()
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $settings = [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['key' => 'site_name', 'value' => 'nmonzzon Studio', 'type' => 'text', 'group' => 'general'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['key' => 'site_description', 'value' => 'Plataforma digital de arte visual - Retratos, Arte en Vivo, Branding, Diseño y Productos', 'type' => 'textarea', 'group' => 'general'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['key' => 'site_logo', 'value' => 'logos/nmonzzon.png', 'type' => 'image', 'group' => 'general'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['key' => 'site_logo_white', 'value' => 'logos/nmonzzon en blanco.png', 'type' => 'image', 'group' => 'general'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['key' => 'contact_email', 'value' => 'info@nmonzzon.com', 'type' => 'text', 'group' => 'contact'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['key' => 'contact_phone', 'value' => '', 'type' => 'text', 'group' => 'contact'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['key' => 'instagram_url', 'value' => 'https://www.instagram.com/nmonzzon/', 'type' => 'text', 'group' => 'social'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['key' => 'facebook_url', 'value' => '', 'type' => 'text', 'group' => 'social'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['key' => 'tiktok_url', 'value' => '', 'type' => 'text', 'group' => 'social'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['key' => 'shipping_base_cost', 'value' => '5.00', 'type' => 'text', 'group' => 'shop'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['key' => 'free_shipping_threshold', 'value' => '50.00', 'type' => 'text', 'group' => 'shop'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['key' => 'tax_rate', 'value' => '21', 'type' => 'text', 'group' => 'shop'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['key' => 'currency', 'value' => 'EUR', 'type' => 'text', 'group' => 'shop'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['key' => 'stripe_public_key', 'value' => '', 'type' => 'text', 'group' => 'payments'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['key' => 'stripe_secret_key', 'value' => '', 'type' => 'text', 'group' => 'payments'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['key' => 'cloudinary_cloud_name', 'value' => '', 'type' => 'text', 'group' => 'media'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['key' => 'cloudinary_api_key', 'value' => '', 'type' => 'text', 'group' => 'media'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['key' => 'cloudinary_api_secret', 'value' => '', 'type' => 'text', 'group' => 'media'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['key' => 'hero_title', 'value' => 'nmonzzon Studio', 'type' => 'text', 'group' => 'home'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['key' => 'hero_subtitle', 'value' => 'Arte visual personalizado', 'type' => 'text', 'group' => 'home'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['key' => 'hero_image', 'value' => '', 'type' => 'image', 'group' => 'home'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['key' => 'about_text', 'value' => 'Artista visual especializada en retratos personalizados, arte en vivo para eventos, branding e ilustración.', 'type' => 'textarea', 'group' => 'home'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['key' => 'maintenance_mode', 'value' => '0', 'type' => 'boolean', 'group' => 'general'],
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ];

        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($settings as $setting) {
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $setting['created_at'] = date('Y-m-d H:i:s');
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $setting['updated_at'] = date('Y-m-d H:i:s');
            // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
            $this->db->table('site_settings')->insert($setting);
        // DELIMITADOR DE BLOQUE
        }
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
