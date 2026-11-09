<?php

namespace App\Services;

use App\Models\Appointment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use PDO;
use Throwable;

class AppointmentCapacity
{
    public const LIMIT_PER_SLOT = 5;

    /**
     * Reserve a slot atomically in the existing SQLite database.
     * The IMMEDIATE transaction acquires SQLite's write reservation
     * before checking occupancy, preventing simultaneous overbooking.
     */
    public static function reserve(array $data, int $userId): Appointment
    {
        $connection = DB::connection();
        $pdo = $connection->getPdo();

        if ($connection->getDriverName() !== 'sqlite') {
            throw new \RuntimeException('This booking lock is designed for SQLite.');
        }

        if ($pdo->inTransaction()) {
            throw new \RuntimeException('Do not call reserve() inside another transaction.');
        }

        $pdo->exec('PRAGMA busy_timeout = 5000');
        $pdo->exec('BEGIN IMMEDIATE');

        try {

        // Convert the submitted time slot into a common session.
            $session = str_starts_with(strtolower($data['time_slot']), 'morning')
                ? 'morning'
                : 'afternoon';
            $booked = Appointment::query()
                
                ->whereDate('consultation_date', $data['consultation_date'])
                ->whereRaw('LOWER(time_slot) LIKE ?', [$session . '%'])
                ->whereRaw("LOWER(status) IN ('pending', 'approved')")
                ->count();

            if ($booked >= self::LIMIT_PER_SLOT) {
                throw ValidationException::withMessages([
                    'time_slot' => 'This time slot is fully booked. Please choose another.',
                ]);
            }

            $appointment = new Appointment();
            $appointment->user_id = $userId;
            $appointment->consultation_date = $data['consultation_date'];
            $appointment->time_slot = $data['time_slot'];
            $appointment->symptoms = $data['symptoms'];
            $appointment->other_service = $data['other_service'] ?? null;
            $appointment->status = 'Pending';
            $appointment->attendance_status = 'unconfirmed';
            $appointment->save();

            $pdo->commit();

            return $appointment;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }
}
