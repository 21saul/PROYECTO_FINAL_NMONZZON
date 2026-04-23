<?php

declare(strict_types=1);

namespace App\Commands;

use App\Models\ScheduledTaskModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Comando Spark: resume el estado de la cola scheduled_tasks para operación diaria.
 */
class CronStatus extends BaseCommand
{
    protected $group       = 'Cron';
    protected $name        = 'cron:status';
    protected $description = 'Muestra resumen de tareas programadas pendientes, vencidas y fallidas.';
    protected $usage       = 'cron:status';

    public function run(array $params)
    {
        $now   = date('Y-m-d H:i:s');

        $pending = model(ScheduledTaskModel::class)
            ->where('processed_at', null)
            ->where('failed_at', null)
            ->countAllResults();
        $due = model(ScheduledTaskModel::class)
            ->where('processed_at', null)
            ->where('failed_at', null)
            ->where('run_at <=', $now)
            ->countAllResults();
        $failed = model(ScheduledTaskModel::class)
            ->where('failed_at IS NOT NULL', null, false)
            ->countAllResults();

        CLI::write('Estado de cron scheduled_tasks', 'yellow');
        CLI::write("Pendientes: {$pending}", 'white');
        CLI::write("Vencidas: {$due}", 'white');
        CLI::write("Fallidas: {$failed}", $failed > 0 ? 'red' : 'green');

        $latestFailed = model(ScheduledTaskModel::class)
            ->where('failed_at IS NOT NULL', null, false)
            ->orderBy('failed_at', 'DESC')
            ->first();

        if ($latestFailed !== null) {
            CLI::newLine();
            CLI::write('Ultimo fallo:', 'yellow');
            CLI::write(
                '  id=' . (int) $latestFailed['id']
                . ' type=' . ($latestFailed['task_type'] ?? '')
                . ' failed_at=' . ($latestFailed['failed_at'] ?? ''),
                'red'
            );
            if (! empty($latestFailed['last_error'])) {
                CLI::write('  error=' . $latestFailed['last_error'], 'dark_gray');
            }
        }
    }
}
