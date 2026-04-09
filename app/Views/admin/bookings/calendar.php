<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/ADMIN/BOOKINGS/CALENDAR.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/admin') ?>

<?php $this->setVar('pageTitle', 'Calendario de reservas') ?>

<?= $this->section('content') ?>

<div class="admin-page-header">
    <h1 class="admin-page-title">Calendario de Reservas</h1>
    <a href="<?= base_url('admin/bookings') ?>" class="btn btn-admin-outline">
        <i class="bi bi-list-ul me-1"></i>Volver al listado
    </a>
</div>

<div class="admin-card">
    <div class="card-body">
        <div id="booking-calendar"></div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('extra_js') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('booking-calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,listWeek' },
        events: '<?= base_url('admin/bookings/calendar-data') ?>',
        eventClick: function(info) {
            window.location.href = '<?= base_url('admin/bookings/') ?>' + info.event.id;
        },
        eventColor: '#c9a96e',
    });
    calendar.render();
});
</script>
<?= $this->endSection() ?>