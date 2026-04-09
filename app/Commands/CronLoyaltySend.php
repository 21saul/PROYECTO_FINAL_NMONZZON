<?php

declare(strict_types=1);

namespace App\Commands;

use App\Libraries\EmailService;
use App\Libraries\LoyaltyClientsService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Envía el correo de reactivación a clientes inactivos (misma lógica que GET api/webhooks/loyalty-clients).
 * Programar en crontab típicamente el día 1 de cada mes (la hora la eliges tú).
 */
class CronLoyaltySend extends BaseCommand
{
    protected $group       = 'Cron';
    protected $name        = 'cron:loyalty-send';
    protected $description = 'Envía emails de fidelización a clientes inactivos 3+ meses.';
    protected $usage       = 'cron:loyalty-send [options]';
    protected $options     = [
        '--dry-run' => 'Lista destinatarios sin enviar',
        '--limit'   => 'Máximo de correos a enviar (por defecto sin límite)',
    ];

    public function run(array $params)
    {
        $dryRun = CLI::getOption('dry-run') !== null;
        $limitOpt = CLI::getOption('limit');
        $limit    = $limitOpt !== null && $limitOpt !== '' ? (int) $limitOpt : 0;

        $clients = (new LoyaltyClientsService())->getInactiveClients(3);
        $total   = count($clients);

        CLI::write("Clientes inactivos (3+ meses): {$total}", 'yellow');

        if ($total === 0) {
            return;
        }

        if ($dryRun) {
            foreach (array_slice($clients, 0, 10) as $c) {
                CLI::write('  - ' . ($c['email'] ?? '') . ' (' . ($c['days_inactive'] ?? '') . ' días)', 'white');
            }
            if ($total > 10) {
                CLI::write('  ... y ' . ($total - 10) . ' más', 'dark_gray');
            }
            CLI::write('Dry-run: no se ha enviado ningún correo.', 'green');

            return;
        }

        $email  = new EmailService();
        $pauseMs = (int) env('LOYALTY_EMAIL_BATCH_PAUSE_MS', 500);
        if ($pauseMs < 0) {
            $pauseMs = 0;
        }

        $sent = 0;
        foreach ($clients as $client) {
            if ($limit > 0 && $sent >= $limit) {
                break;
            }
            try {
                if ($email->sendLoyaltyReactivation($client)) {
                    CLI::write('Enviado: ' . ($client['email'] ?? ''), 'green');
                    $sent++;
                } else {
                    CLI::write('Fallo envío: ' . ($client['email'] ?? ''), 'red');
                }
            } catch (\Throwable $e) {
                CLI::write('Error ' . ($client['email'] ?? '') . ': ' . $e->getMessage(), 'red');
            }
            if ($pauseMs > 0) {
                usleep($pauseMs * 1000);
            }
        }

        CLI::write("Total enviados en esta ejecución: {$sent}", 'yellow');
    }
}
