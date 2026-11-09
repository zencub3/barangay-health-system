<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\User;

// Public Welcome Route
Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);

// Smart Dashboard Redirection based on Role
Route::get('/dashboard', function () {
    $role = strtolower(trim(auth()->user()->role ?? ''));
    
    if ($role === 'healthcare_personnel') {
        return redirect()->route('staff.dashboard');
    } elseif ($role === 'bhw') {
        return redirect()->route('bhw.dashboard');
    } elseif ($role === 'patient') {
        return redirect()->route('patient.dashboard');
    }
    
    // Fallback if role is missing or invalid
    auth()->logout();
    return redirect()->route('login')->withErrors(['email' => 'User account does not have a valid assigned role.']);
})->middleware(['auth', 'verified'])->name('dashboard');

// Authenticated Group (Patients Only)
Route::middleware(['auth'])->group(function () {
    
    Route::get('/patient/dashboard', function () {
        $role = strtolower(trim(auth()->user()->role ?? ''));
        
        if ($role !== 'patient') {
            if ($role === 'healthcare_personnel') {
                return redirect()->route('staff.dashboard');
            } elseif ($role === 'bhw') {
                return redirect()->route('bhw.dashboard');
            }
            return redirect()->route('login')->withErrors(['email' => 'Unauthorized role type.']);
        }

        $userId = auth()->id();
        $appointments = Appointment::where('user_id', $userId)->latest()->get();
        return view('patient.dashboard', compact('appointments'));
    })->name('patient.dashboard');

    Route::match(['get', 'post'], '/appointments/create', function (Request $request) {
        $role = strtolower(trim(auth()->user()->role ?? ''));
        if ($role !== 'patient') {
            return redirect()->route('staff.dashboard');
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'consultation_date' => 'required|date',
                'time_slot' => 'required|string',
                'symptoms' => 'required|string',
                'other_service' => 'required_if:symptoms,Others (General Checkup)|nullable|string|max:255',
            ]);

            Appointment::create([
                'user_id' => auth()->id(),
                'consultation_date' => $request->consultation_date,
                'time_slot' => $request->time_slot,
                'symptoms' => $request->symptoms,
                'other_service' => $request->symptoms === 'Others (General Checkup)' ? $request->other_service : null,
                'status' => 'Pending',
            ]);

            return redirect()->route('patient.dashboard')->with('success', 'Appointment submitted successfully!');
        }

        return view('patient.appointments.create');
    })->name('appointments.create');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ========================================================
// Barangay Health Worker (BHW) Dashboard & Verification Group
// ========================================================
Route::middleware(['auth'])->group(function () {
    Route::get('/bhw/dashboard', function () {
        $role = strtolower(trim(auth()->user()->role ?? ''));
        
        if ($role !== 'bhw') {
            if ($role === 'healthcare_personnel') {
                return redirect()->route('staff.dashboard');
            } elseif ($role === 'patient') {
                return redirect()->route('patient.dashboard');
            }
            return redirect()->route('login')->withErrors(['email' => 'Unauthorized role type.']);
        }

        // Fetch patients pending residency verification for Ugac Sur
        $pendingPatients = User::where('role', 'patient')
            ->where('status', 'pending')
            ->latest()
            ->get();

        // Fetch approved appointments for monitoring upcoming/outgoing visits
        $appointments = Appointment::with('user')->latest()->get();

        // Analytics calculation (same as healthcare personnel)
        $currentYear = date('Y');
        $yearAppointments = Appointment::whereYear('created_at', $currentYear)
            ->where('status', 'Approved')
            ->get();
        $totalYearCount = $yearAppointments->count();

        $symptomStats = [];
        if ($totalYearCount > 0) {
            $grouped = $yearAppointments->groupBy('symptoms');
            foreach ($grouped as $symptom => $group) {
                $count = $group->count();
                $percentage = round(($count / $totalYearCount) * 100);
                $symptomStats[] = [
                    'label' => $symptom,
                    'count' => $count,
                    'percentage' => $percentage,
                ];
            }
        }

        return view('bhw.dashboard', compact('pendingPatients', 'appointments', 'symptomStats', 'currentYear', 'totalYearCount'));
    })->name('bhw.dashboard');

    Route::patch('/bhw/users/{user}/verify', function (Request $request, User $user) {
        $role = strtolower(trim(auth()->user()->role ?? ''));
        if ($role !== 'bhw') {
            return redirect()->route('dashboard');
        }

        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $user->update([
            'status' => $request->status,
        ]);

        return redirect()->route('bhw.dashboard')->with('success', 'Patient residency status updated successfully!');
    })->name('bhw.users.verify');
});

// ========================================================
// Healthcare Personnel Dashboard & Appointment Management Group
// ========================================================
Route::middleware(['auth'])->group(function () {
    Route::get('/staff/dashboard', function () {
        $role = strtolower(trim(auth()->user()->role ?? ''));
        
        if ($role !== 'healthcare_personnel') {
            if ($role === 'patient') {
                return redirect()->route('patient.dashboard');
            } elseif ($role === 'bhw') {
                return redirect()->route('bhw.dashboard');
            }
            return redirect()->route('login')->withErrors(['email' => 'Unauthorized role type.']);
        }

        $appointments = Appointment::with('user')->latest()->get();
        
        $currentYear = date('Y');
        $yearAppointments = Appointment::whereYear('created_at', $currentYear)
            ->where('status', 'Approved')
            ->get();
        $totalYearCount = $yearAppointments->count();

        $symptomStats = [];
        if ($totalYearCount > 0) {
            $grouped = $yearAppointments->groupBy('symptoms');
            foreach ($grouped as $symptom => $group) {
                $count = $group->count();
                $percentage = round(($count / $totalYearCount) * 100);
                $symptomStats[] = [
                    'label' => $symptom,
                    'count' => $count,
                    'percentage' => $percentage,
                ];
            }
        }

        return view('staff.dashboard', compact('appointments', 'symptomStats', 'currentYear', 'totalYearCount'));
    })->name('staff.dashboard');

    Route::patch('/appointments/{appointment}/update', function (Request $request, Appointment $appointment) {
        $role = strtolower(trim(auth()->user()->role ?? ''));
        if ($role !== 'healthcare_personnel') {
            return redirect()->route('patient.dashboard');
        }

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'nullable|string|max:255',
        ]);

        $appointment->update([
            'status' => ucfirst($request->status),
            'rejection_reason' => strtolower($request->status) === 'rejected' ? $request->rejection_reason : null,
        ]);

        return redirect()->route('staff.dashboard')->with('success', 'Appointment status updated successfully!');
    })->name('staff.appointment.update');
});

require __DIR__.'/auth.php';