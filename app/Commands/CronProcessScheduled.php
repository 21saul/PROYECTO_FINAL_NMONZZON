<?php

declare(strict_types=1);

namespace App\Commands;

use App\Libraries\EmailService;
use App\Libraries\ScheduledTaskService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Procesa filas de scheduled_tasks (p. ej. recordatorio de reserva Arte en vivo).
 * Conviene ejecutarlo cada hora o cada 15 minutos.
 */
class CronProcessScheduled extends BaseCommand
{
    protected $group       = 'Cron';
    protected $name        = 'cron:process-scheduled';
    protected $description = 'Ejecuta tareas programadas vencidas (scheduled_tasks).';
    protected $usage       = 'cron:process-scheduled [options]';
    protected $options     = [
        '--limit' => 'Máximo de tareas a procesar (default 50)',
    ];

    public function run(array $params)
    {
        $limitOpt = CLI::getOption('limit');
        $limit    = $limitOpt !== null && $limitOpt !== '' ? max(1, (int) $limitOpt) : 50;

        $service = new ScheduledTaskService();
        $email   = new EmailService();
        $tasks   = $service->fetchDueTasks($limit);

        if ($tasks === []) {
            CLI::write('No hay tareas pendientes vencidas.', 'dark_gray');

            return;
        }

        CLI::write('Procesando ' . count($tasks) . ' tarea(s)...', 'yellow');

        foreach ($tasks as $task) {
            $ok = $service->processTaskRow($task, $email);
            $id = (int) ($task['id'] ?? 0);
            if ($ok) {
                CLI::write("  OK id={$id} type={$task['task_type']}", 'green');
            } else {
                CLI::write("  Reintentar id={$id} type={$task['task_type']} (email falló)", 'red');
            }
        }
    }
}
