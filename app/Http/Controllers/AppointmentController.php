<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    // Show appointments for the logged-in patient
    public function patientIndex()
    {
        $appointments = auth()->user()->appointments()->latest()->get();
        return view('patient.dashboard', compact('appointments'));
    }

    // Store a new appointment request submitted by a patient (with server-side validation)
    public function store(Request $request)
    {
        $request->validate([
            'preferred_date' => 'required|date|after:today',
            'symptoms' => 'required|string|max:500',
        ]);

        auth()->user()->appointments()->create($request->only('preferred_date', 'symptoms'));

        return back()->with('success', 'Appointment request submitted successfully.');
    }

    // Show all appointments for staff (Healthcare Personnel / BHW / Admin)
    public function staffIndex()
    {
        $appointments = Appointment::with('patient')->latest()->get();
        return view('staff.dashboard', compact('appointments'));
    }

    // Approve or reject an appointment request
    public function updateStatus(Request $request, Appointment $appointment)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'nullable|required_if:status,rejected|string|max:255',
        ]);

        $appointment->update([
            'status' => $request->status,
            'rejection_reason' => $request->status === 'rejected' ? $request->rejection_reason : null,
        ]);

        return back()->with('success', 'Appointment status updated successfully.');
    }
}