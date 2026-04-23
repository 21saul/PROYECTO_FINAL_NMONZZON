<?php

namespace Tests\Unit\Libraries;

use App\Libraries\ScheduledTaskService;
use CodeIgniter\Test\CIUnitTestCase;

class ScheduledTaskServiceTest extends CIUnitTestCase
{
    private ScheduledTaskService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ScheduledTaskService();
    }

    public function testShouldFailTaskWhenNextAttemptExhaustsLimit(): void
    {
        $task = [
            'attempts' => 4,
            'max_attempts' => 5,
        ];

        $this->assertTrue($this->service->shouldFailTask($task));
    }

    public function testShouldRetryTaskWhenAttemptsRemain(): void
    {
        $task = [
            'attempts' => 1,
            'max_attempts' => 5,
        ];

        $this->assertFalse($this->service->shouldFailTask($task));
    }
}
