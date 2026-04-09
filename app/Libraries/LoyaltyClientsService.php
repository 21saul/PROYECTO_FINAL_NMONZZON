<?php

declare(strict_types=1);

namespace App\Libraries;

/**
 * Clientes con rol client, activos, sin pedido tienda ni retrato en los últimos N meses.
 */
class LoyaltyClientsService
{
    /**
     * @return list<array{name: string, email: string, last_activity: string, days_inactive: int}>
     */
    public function getInactiveClients(int $inactiveMonths = 3): array
    {
        $userModel    = model('UserModel');
        $orderModel   = model('OrderModel');
        $portraitModel = model('PortraitOrderModel');

        $threshold = date('Y-m-d H:i:s', strtotime("-{$inactiveMonths} months"));
        $clients   = $userModel->where('role', 'client')->where('is_active', 1)->findAll();

        $loyaltyClients = [];

        foreach ($clients as $client) {
            $lastOrder = $orderModel->where('user_id', $client['id'])
                ->orderBy('created_at', 'DESC')
                ->first();
            $lastPortrait = $portraitModel->where('user_id', $client['id'])
                ->orderBy('created_at', 'DESC')
                ->first();

            $lastActivity = max(
                $lastOrder['created_at'] ?? '2000-01-01',
                $lastPortrait['created_at'] ?? '2000-01-01'
            );

            if ($lastActivity < $threshold && $lastActivity > '2000-01-01') {
                $loyaltyClients[] = [
                    'name'           => $client['name'],
                    'email'          => $client['email'],
                    'last_activity'  => $lastActivity,
                    'days_inactive'  => (int) ((time() - strtotime($lastActivity)) / 86400),
                ];
            }
        }

        return $loyaltyClients;
    }
}
