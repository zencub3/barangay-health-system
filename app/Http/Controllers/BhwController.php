
<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;

class BhwController extends Controller
{
    public function index(Request $request)
    {
        // Allow BHW accounts only
        abort_unless(
            $request->user()?->role === 'barangay_health_worker',
            403
        );

        // Upcoming appointments
        $upcoming = Appointment::with('user')
            ->whereDate('consultation_date', '>=', today())
            ->orderBy('consultation_date')
            ->get();

        // Past appointments
        $past = Appointment::with('user')
            ->whereDate('consultation_date', '<', today())
            ->orderByDesc('consultation_date')
            ->get();

        // Pending patient registrations
        $pendingResidents = User::where('role', 'patient')
            ->where('residency_status', 'pending')
            ->orderBy('created_at')
            ->get();

        return view('bhw.dashboard', compact(
            'upcoming',
            'past',
            'pendingResidents'
        ));
    }
}
