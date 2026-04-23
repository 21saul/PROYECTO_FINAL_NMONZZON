<?php

namespace Tests\Unit\Libraries;

use App\Libraries\LoyaltyClientsService;
use CodeIgniter\Test\CIUnitTestCase;

class LoyaltyClientsServiceTest extends CIUnitTestCase
{
    private LoyaltyClientsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new LoyaltyClientsService();
    }

    public function testShouldSendLoyaltyEmailForInactiveClientWithoutPreviousSend(): void
    {
        $client = [
            'id' => 10,
            'loyalty_last_activity_at' => null,
        ];

        $this->assertTrue(
            $this->service->shouldSendLoyaltyEmail($client, '2025-12-01 10:00:00', '2026-03-01 00:00:00')
        );
    }

    public function testShouldNotSendLoyaltyEmailWhenClientAlreadyCoveredForSameActivity(): void
    {
        $client = [
            'id' => 10,
            'loyalty_last_activity_at' => '2025-12-01 10:00:00',
        ];

        $this->assertFalse(
            $this->service->shouldSendLoyaltyEmail($client, '2025-12-01 10:00:00', '2026-03-01 00:00:00')
        );
    }

    public function testShouldNotSendLoyaltyEmailWhenActivityIsStillRecent(): void
    {
        $client = [
            'id' => 10,
            'loyalty_last_activity_at' => null,
        ];

        $this->assertFalse(
            $this->service->shouldSendLoyaltyEmail($client, '2026-03-15 10:00:00', '2026-03-01 00:00:00')
        );
    }
}
