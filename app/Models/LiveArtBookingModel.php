<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * LIVEARTBOOKINGMODEL — MODELO CODEIGNITER 4 (CAPA DE DATOS)
 * =============================================================================
 * TABLA PRINCIPAL: LIVE_ART_BOOKINGS.
 * QUÉ HACE: MAPEA FILAS SQL, CAMPOS PERMITIDOS, VALIDACIÓN Y CALLBACKS DE INSERCIÓN/ACTUALIZACIÓN.
 * POR QUÉ ASÍ: EVITA REPETIR SQL Y REGLAS EN CONTROLADORES; UN MODELO POR TABLA (PATRÓN CI4).
 * =============================================================================
 */

namespace App\Models;

use CodeIgniter\Model;

class LiveArtBookingModel extends Model
{
    protected $table            = 'live_art_bookings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'booking_number',
        'user_id',
        'contact_name',
        'contact_email',
        'contact_phone',
        'event_type',
        'event_date',
        'event_start_time',
        'event_end_time',
        'event_location',
        'event_city',
        'event_postal_code',
        'num_guests',
        'num_portraits',
        'travel_distance_km',
        'base_rate',
        'travel_fee',
        'total_quote',
        'status',
        'special_requirements',
        'admin_notes',
        'logistics_checklist',
        'stripe_payment_id',
        'payment_status',
        'invoice_path',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'contact_name'   => 'required|max_length[100]',
        'contact_email'  => 'required|valid_email',
        'event_date'     => 'required|valid_date',
        'event_location' => 'required',
        'event_city'     => 'required',
        'event_type'     => 'in_list[wedding,corporate,birthday,festival,private,other]',
        'status'         => 'in_list[pending,quoted,confirmed,deposit_paid,completed,cancelled]',
    ];

    protected $beforeInsert = ['generateBookingNumber'];

    protected function generateBookingNumber(array $data)
    {
        $data['data']['booking_number'] = 'LA-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

        return $data;
    }
}