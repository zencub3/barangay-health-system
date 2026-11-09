
<?php

use App\Http\Controllers\ProfileController;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/* Public homepage and role-based dashboard*/

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

    /*Patient dashboard and appointment booking*/

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
                'time_slot' => ['required', 'string', 'regex:/^(Morning|Afternoon)(\s*\(.*\))?$/i'],
                'symptoms' => 'required|string',
                'other_service' => 'nullable|string|max:255',
            ]);

            // Only change to appointment saving:
            // Check and reserve the slot safely before saving.
            \App\Services\AppointmentCapacity::reserve(
                $data,
                auth()->id()
            );

            return redirect()->route('patient.dashboard')
                ->with('success', 'Appointment submitted successfully!');
        }

        return view('patient.appointments.create');
    })->name('appointments.create');

    /*Appointment availability for the calendar*/

    Route::get('/appointments/availability', function (Request $request) {
        abort_unless(auth()->user()->role === 'patient', 403);

        abort_unless(
            strtolower(auth()->user()->status ?? '') === 'approved',
            403,
            'Your account is awaiting barangay residency verification.'
        );

        $data = $request->validate([
            'month' => ['required', 'date_format:Y-m'],
        ]);

        $first = Carbon::createFromFormat(
            '!Y-m-d',
            $data['month'] . '-01'
        )->startOfMonth();

        $last = $first->copy()->endOfMonth();

        // Count Pending and Approved appointments only.
        $rows = DB::table('appointments')
            ->selectRaw("
                consultation_date,
                CASE
                    WHEN LOWER(time_slot) LIKE 'morning%' THEN 'morning'
                    WHEN LOWER(time_slot) LIKE 'afternoon%' THEN 'afternoon'
                    ELSE 'other'
                END AS session,
                COUNT(*) AS booked
            ")
            ->whereBetween('consultation_date', [
                $first->toDateString(),
                $last->toDateString(),
            ])
            ->whereRaw("LOWER(status) IN ('pending', 'approved')")
            ->groupBy('consultation_date')
            ->groupByRaw("
                CASE
                    WHEN LOWER(time_slot) LIKE 'morning%' THEN 'morning'
                    WHEN LOWER(time_slot) LIKE 'afternoon%' THEN 'afternoon'
                    ELSE 'other'
                END
            ")
            ->get();

        $days = [];

        foreach ($rows as $row) {
            $day = substr((string) $row->consultation_date, 0, 10);

            $slot = match (strtolower($row->session)) {
                'morning' => 'Morning',
                'afternoon' => 'Afternoon',
                default => null,
            };

            if ($slot !== null) {
                $days[$day][$slot] = (int) $row->booked;
            }
        }

        return response()->json(['days' => $days]);
    })->name('appointments.availability');

    /*Barangay Health Worker: dashboard and resident review*/

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
            $status = strtolower(trim(
                (string) ($appointment->status ?? 'pending')
            ));

            $attendance = strtolower(trim(
                (string) ($appointment->attendance_status ?? 'unconfirmed')
            ));

            return $status !== 'rejected'
                && !in_array($attendance, ['attended', 'no_show'], true);
        })->values();

        // History: attended, no-show, or rejected appointments.
        $past = $appointments->filter(function ($appointment) {
            $status = strtolower(trim(
                (string) ($appointment->status ?? 'pending')
            ));

            $attendance = strtolower(trim(
                (string) ($appointment->attendance_status ?? 'unconfirmed')
            ));

            return $status === 'rejected'
                || in_array($attendance, ['attended', 'no_show'], true);
        })->sortByDesc('consultation_date')->values();

        // Only approved appointments on or before today.
        $attendanceQueue = Appointment::with('user')
            ->where('status', 'Approved')
            ->whereDate('consultation_date', '<=', today())
            ->where(function ($query) {
                $query->whereNull('attendance_status')
                    ->orWhere('attendance_status', 'unconfirmed');
            })
            ->orderBy('consultation_date')
            ->get();

        // Analytics: confirmed attendance only.
        $currentYear = now()->year;

        $attendedAppointments = Appointment::where('status', 'Approved')
            ->where('attendance_status', 'attended')
            ->whereDate('consultation_date', '<=', today())
            ->whereYear('consultation_date', $currentYear)
            ->get();

        $totalYearCount = $attendedAppointments->count();

        $symptomStats = [];

        if ($totalYearCount > 0) {
            foreach (
                $attendedAppointments->groupBy('symptoms')
                as $symptom => $group
            ) {
                $count = $group->count();

                $symptomStats[] = [
                    'label' => $symptom ?: 'Others',
                    'count' => $count,
                    'percentage' => round(
                        ($count / $totalYearCount) * 100
                    ),
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

    Route::patch(
        '/bhw/users/{user}/verify',
        function (Request $request, User $user) {
            abort_unless(auth()->user()->role === 'bhw', 403);

            $validated = $request->validate([
                'status' => 'required|in:approved,rejected',
            ]);

            DB::transaction(function () use ($user, $validated) {
                $patient = User::whereKey($user->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                abort_unless(
                    $patient->role === 'patient'
                    && $patient->status === 'pending',
                    409,
                    'This registration has already been reviewed.'
                );

                $patient->status = $validated['status'];
                $patient->save();
            });

            return redirect()->route('bhw.dashboard')
                ->with(
                    'success',
                    'Patient residency verification updated successfully!'
                );
        }
    )->name('bhw.users.verify');

    /*BHW attendance confirmation*/

    Route::patch(
        '/bhw/appointments/{appointment}/attendance',
        function (Request $request, Appointment $appointment) {
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

                $date = Carbon::parse(
                    $record->consultation_date
                )->startOfDay();

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
                    in_array(
                        $record->attendance_status,
                        [null, 'unconfirmed'],
                        true
                    ),
                    409,
                    'Attendance has already been confirmed.'
                );

                $record->attendance_status =
                    $validated['attendance_status'];

                $record->attendance_verified_by = auth()->id();
                $record->attendance_verified_at = now();
                $record->save();
            });

            return redirect()->route('bhw.dashboard')
                ->with(
                    'success',
                    'Appointment attendance recorded successfully!'
                );
        }
    )->name('bhw.appointments.attendance');

    /*Healthcare Personnel: dashboard and appointment approvals*/

    Route::get('/staff/dashboard', function () {
        abort_unless(
            auth()->user()->role === 'healthcare_personnel',
            403
        );

        $appointments = Appointment::with('user')
            ->latest()
            ->get();

        $currentYear = now()->year;

        $attendedAppointments = Appointment::where('status', 'Approved')
            ->where('attendance_status', 'attended')
            ->whereDate('consultation_date', '<=', today())
            ->whereYear('consultation_date', $currentYear)
            ->get();

        $totalYearCount = $attendedAppointments->count();

        $symptomStats = [];

        if ($totalYearCount > 0) {
            foreach (
                $attendedAppointments->groupBy('symptoms')
                as $symptom => $group
            ) {
                $count = $group->count();

                $symptomStats[] = [
                    'label' => $symptom ?: 'Others',
                    'count' => $count,
                    'percentage' => round(
                        ($count / $totalYearCount) * 100
                    ),
                ];
            }
        }

        return view('staff.dashboard', compact(
            'appointments',
            'symptomStats',
            'currentYear',
            'totalYearCount'
        ));
    })->name('staff.dashboard');

    Route::match(
        ['post', 'patch'],
        '/appointments/{appointment}/update',
        function (Request $request, Appointment $appointment) {
            abort_unless(
                auth()->user()->role === 'healthcare_personnel',
                403,
                'Only Healthcare Personnel can update appointments.'
            );

            $validated = $request->validate([
                'status' => 'required|in:Approved,Rejected,approved,rejected',
                'rejection_reason' =>
                    'required_if:status,Rejected,rejected|nullable|string|max:255',
            ]);

            abort_unless(
                strtolower($appointment->status) === 'pending',
                409,
                'This appointment has already been processed.'
            );

            $status = ucfirst(strtolower($validated['status']));

            $appointment->status = $status;

            $appointment->rejection_reason =
                $status === 'Rejected'
                    ? $validated['rejection_reason']
                    : null;

            $appointment->save();

            return redirect()->route('staff.dashboard')
                ->with(
                    'success',
                    'Appointment status updated successfully!'
                );
        }
    )->name('appointments.update-status');

    /*Profile management*/

    Route::get('/profile', [
        ProfileController::class, 'edit'
    ])->name('profile.edit');

    Route::patch('/profile', [
        ProfileController::class, 'update'
    ])->name('profile.update');

    Route::delete('/profile', [
        ProfileController::class, 'destroy'
    ])->name('profile.destroy');
});


require __DIR__.'/auth.php';
