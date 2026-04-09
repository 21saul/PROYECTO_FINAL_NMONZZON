<?php

declare(strict_types=1);

namespace App\Libraries;

use App\Models\LiveArtBookingModel;
use App\Models\ScheduledTaskModel;

/**
 * Encola tareas diferidas procesadas por el comando cron:process-scheduled.
 */
class ScheduledTaskService
{
    /**
     * Una fila por reserva: recordatorio interno si sigue en pending tras BOOKING_FOLLOWUP_DELAY_HOURS.
     */
    public function enqueueLiveArtBookingFollowup(int $bookingId): void
    {
        $hours = (int) env('BOOKING_FOLLOWUP_DELAY_HOURS', 36);
        if ($hours < 1) {
            $hours = 36;
        }

        $model = model(ScheduledTaskModel::class);
        $exists = $model
            ->where('task_type', ScheduledTaskModel::TYPE_LIVE_ART_BOOKING_FOLLOWUP)
            ->where('reference_id', $bookingId)
            ->first();
        if ($exists !== null) {
            return;
        }

        $runAt = date('Y-m-d H:i:s', strtotime("+{$hours} hours"));

        $model->insert([
            'task_type'    => ScheduledTaskModel::TYPE_LIVE_ART_BOOKING_FOLLOWUP,
            'reference_id' => $bookingId,
            'run_at'       => $runAt,
        ]);
    }

    /**
     * Marca la tarea como hecha (éxito o descarte).
     */
    public function markProcessed(int $taskId): void
    {
        model(ScheduledTaskModel::class)->update($taskId, [
            'processed_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function fetchDueTasks(int $limit = 50): array
    {
        return model(ScheduledTaskModel::class)
            ->where('processed_at', null)
            ->where('run_at <=', date('Y-m-d H:i:s'))
            ->orderBy('run_at', 'ASC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Ejecuta una fila de scheduled_tasks según task_type.
     */
    public function processTaskRow(array $task, EmailService $email): bool
    {
        $id = (int) $task['id'];

        if ($task['task_type'] === ScheduledTaskModel::TYPE_LIVE_ART_BOOKING_FOLLOWUP) {
            return $this->processLiveArtBookingFollowup($task, $email);
        }

        log_message('warning', "ScheduledTaskService: tipo desconocido '{$task['task_type']}', id={$id}");
        $this->markProcessed($id);

        return true;
    }

    private function processLiveArtBookingFollowup(array $task, EmailService $email): bool
    {
        $taskId      = (int) $task['id'];
        $bookingId   = (int) $task['reference_id'];
        $bookingModel = model(LiveArtBookingModel::class);
        $booking     = $bookingModel->find($bookingId);

        if ($booking === null) {
            $this->markProcessed($taskId);

            return true;
        }

        if (($booking['status'] ?? '') !== 'pending') {
            $this->markProcessed($taskId);

            return true;
        }

        try {
            $email->sendLiveArtBookingFollowupReminderToAdmin($booking);
        } catch (\Throwable $e) {
            log_message('error', 'Booking follow-up email failed: ' . $e->getMessage());

            return false;
        }

        $this->markProcessed($taskId);

        return true;
    }
}
