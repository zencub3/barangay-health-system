<?php

use App\Http\Controllers\ProfileController;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public homepage and role-based dashboard
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $role = strtolower(trim(auth()->user()->role ?? ''));

    return match ($role) {
        'patient' => redirect()->route('patient.dashboard'),
        'bhw' => redirect()->route('bhw.dashboard'),
        'healthcare_personnel' => redirect()->route('staff.dashboard'),
        default => abort(403, 'Your account role is not authorized.'),
    };
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    /*
    |----------------------------------------------------------------------
    | Patient dashboard and appointment booking
    |----------------------------------------------------------------------
    */

    Route::get('/patient/dashboard', function () {
        abort_unless(auth()->user()->role === 'patient', 403);
        abort_unless(
            strtolower(auth()->user()->status ?? '') === 'approved',
            403,
            'Your account is awaiting barangay residency verification.'
        );

        $appointments = Appointment::where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('patient.dashboard', compact('appointments'));
    })->name('patient.dashboard');

    Route::match(['get', 'post'], '/appointments/create', function (Request $request) {
        abort_unless(auth()->user()->role === 'patient', 403);
        abort_unless(
            strtolower(auth()->user()->status ?? '') === 'approved',
            403,
            'Your account is awaiting barangay residency verification.'
        );

        if ($request->isMethod('post')) {
            $data = $request->validate([
                'consultation_date' => 'required|date|after_or_equal:today',
                'time_slot' => 'required|string',
                'symptoms' => 'required|string',
                'other_service' => 'nullable|string|max:255',
            ]);

            // Assign explicitly; does not depend on model mass-assignment settings.
            $appointment = new Appointment();
            $appointment->user_id = auth()->id();
            $appointment->consultation_date = $data['consultation_date'];
            $appointment->time_slot = $data['time_slot'];
            $appointment->symptoms = $data['symptoms'];
            $appointment->other_service = $data['other_service'] ?? null;
            $appointment->status = 'Pending';
            $appointment->attendance_status = 'unconfirmed';
            $appointment->save();

            return redirect()->route('patient.dashboard')
                ->with('success', 'Appointment submitted successfully!');
        }

        return view('patient.appointments.create');
    })->name('appointments.create');
    
    /*
    |----------------------------------------------------------------------
    | Barangay Health Worker: dashboard and resident review
    |----------------------------------------------------------------------
    */

    Route::get('/bhw/dashboard', function () {
        abort_unless(auth()->user()->role === 'bhw', 403);

        $pendingPatients = User::where('role', 'patient')
            ->where('status', 'pending')
            ->latest()
            ->get();
        $pendingResidents = $pendingPatients;

        $appointments = Appointment::with('user')
            ->orderBy('consultation_date')
            ->get();



            $upcoming = $appointments->filter(function ($appointment) {
            $status = strtolower(trim((string) ($appointment->status ?? 'pending')));
            $attendance = strtolower(trim((string) ($appointment->attendance_status ?? 'unconfirmed')));

            return $status !== 'rejected'
                && !in_array($attendance, ['attended', 'no_show'], true);
        })->values();

        // History: confirmed attended visits, no-shows, or rejected requests.
        // Attended appointments move here even if their scheduled date is today.
        $past = $appointments->filter(function ($appointment) {
            $status = strtolower(trim((string) ($appointment->status ?? 'pending')));
            $attendance = strtolower(trim((string) ($appointment->attendance_status ?? 'unconfirmed')));

            return $status === 'rejected'
                || in_array($attendance, ['attended', 'no_show'], true);
        })->sortByDesc('consultation_date')->values();

        // Only approved appointments on or before today can be verified.
        $attendanceQueue = Appointment::with('user')
            ->where('status', 'Approved')
            ->whereDate('consultation_date', '<=', today())
            ->where(function ($query) {
                $query->whereNull('attendance_status')
                    ->orWhere('attendance_status', 'unconfirmed');
            })
            ->orderBy('consultation_date')
            ->get();

        // Count only approved appointments whose attendance was confirmed.
        $currentYear = now()->year;
        $attendedAppointments = Appointment::where('status', 'Approved')
            ->where('attendance_status', 'attended')
            ->whereDate('consultation_date', '<=', today())
            ->whereYear('consultation_date', $currentYear)
            ->get();

             $totalYearCount = $attendedAppointments->count();
        $symptomStats = [];

        if ($totalYearCount > 0) {
            foreach ($attendedAppointments->groupBy('symptoms') as $symptom => $group) {
                $count = $group->count();
                $symptomStats[] = [
                    'label' => $symptom ?: 'Others',
                    'count' => $count,
                    'percentage' => round(($count / $totalYearCount) * 100),
                ];
            }
        }

        return view('bhw.dashboard', compact(
            'pendingPatients',
            'pendingResidents',
            'appointments',
            'upcoming',
            'past',
            'attendanceQueue',
            'symptomStats',
            'currentYear',
            'totalYearCount'
        ));
    })->name('bhw.dashboard');

    Route::patch('/bhw/users/{user}/verify', function (Request $request, User $user) {
        abort_unless(auth()->user()->role === 'bhw', 403);

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        DB::transaction(function () use ($user, $validated) {
            $patient = User::whereKey($user->id)->lockForUpdate()->firstOrFail();

            abort_unless(
                $patient->role === 'patient' && $patient->status === 'pending',
                409,
                'This registration has already been reviewed.'
            );

            $patient->status = $validated['status'];
            $patient->save();
        });

         return redirect()->route('bhw.dashboard')
            ->with('success', 'Patient residency verification updated successfully!');
    })->name('bhw.users.verify');

    Route::patch('/bhw/appointments/{appointment}/attendance', function (Request $request, Appointment $appointment) {
        abort_unless(
            auth()->user()->role === 'bhw',
            403,
            'Only BHW users can confirm attendance.'
        );

        $validated = $request->validate([
            'attendance_status' => 'required|in:attended,no_show',
        ]);

        DB::transaction(function () use ($appointment, $validated) {
            $record = Appointment::whereKey($appointment->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                $record->status === 'Approved',
                403,
                'Only approved appointments can have attendance confirmed.'
            );

            $date = Carbon::parse($record->consultation_date)->startOfDay();

            abort_unless(
                $date->lte(today()),
                403,
                'Future appointments cannot be marked as attended.'
            );

    
            if ($validated['attendance_status'] === 'no_show') {
                abort_unless(
                    $date->lt(today()),
                    403,
                    'No-show may only be recorded after the scheduled day.'
                );
            }

            abort_unless(
                in_array($record->attendance_status, [null, 'unconfirmed'], true),
                409,
                'Attendance has already been confirmed.'
            );

             $record->attendance_status = $validated['attendance_status'];
            $record->attendance_verified_by = auth()->id();
            $record->attendance_verified_at = now();
            $record->save();
        });

        return redirect()->route('bhw.dashboard')
            ->with('success', 'Appointment attendance recorded successfully!');
    })->name('bhw.appointments.attendance');

    /*
    |----------------------------------------------------------------------
    | Healthcare Personnel: dashboard and appointment approvals
    |----------------------------------------------------------------------
    */

    Route::get('/staff/dashboard', function () {
        abort_unless(auth()->user()->role === 'healthcare_personnel', 403);

        $appointments = Appointment::with('user')->latest()->get();
        $currentYear = now()->year;

        // Same confirmed attendance metric as the BHW dashboard.
        $attendedAppointments = Appointment::where('status', 'Approved')
            ->where('attendance_status', 'attended')
            ->whereDate('consultation_date', '<=', today())
            ->whereYear('consultation_date', $currentYear)
            ->get();

            $totalYearCount = $attendedAppointments->count();
        $symptomStats = [];

        if ($totalYearCount > 0) {
            foreach ($attendedAppointments->groupBy('symptoms') as $symptom => $group) {
                $count = $group->count();
                $symptomStats[] = [
                    'label' => $symptom ?: 'Others',
                    'count' => $count,
                    'percentage' => round(($count / $totalYearCount) * 100),
                ];
            }
        }

        return view('staff.dashboard', compact(
            'appointments', 'symptomStats', 'currentYear', 'totalYearCount'
        ));
    })->name('staff.dashboard');

    Route::match(['post', 'patch'], '/appointments/{appointment}/update', function (Request $request, Appointment $appointment) {
        abort_unless(
            auth()->user()->role === 'healthcare_personnel',
            403,
            'Only Healthcare Personnel can update appointments.'
        );

        $validated = $request->validate([
            'status' => 'required|in:Approved,Rejected,approved,rejected',
            'rejection_reason' => 'required_if:status,Rejected,rejected|nullable|string|max:255',
        ]);

        abort_unless(
            strtolower($appointment->status) === 'pending',
            409,
            'This appointment has already been processed.'
        );

        $status = ucfirst(strtolower($validated['status']));
        $appointment->status = $status;
        $appointment->rejection_reason = $status === 'Rejected'
            ? $validated['rejection_reason']
            : null;
        $appointment->save();

        return redirect()->route('staff.dashboard')
            ->with('success', 'Appointment status updated successfully!');
    })->name('appointments.update-status');

    /*
    |----------------------------------------------------------------------
    | Profile management
    |----------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Laravel Breeze authentication routes. Do not duplicate /login here.
require __DIR__.'/auth.php';