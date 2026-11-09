<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Patient Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Session Success Message Notification -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- 1. Quick Action: Select Appointment Scheduling -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900">
                <h3 class="text-lg font-medium mb-2">Book a Consultation</h3>
                <p class="text-sm text-gray-600 mb-4">Choose your preferred date, available time slot, and presenting symptoms or reason for visit.</p>
                <a href="{{ route('appointments.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                    Schedule Appointment
                </a>
            </div>

            <!-- 2. Appointment Status Tracker (Approved / Pending / Rejection Notice) -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900">
                <h3 class="text-lg font-medium mb-4">My Appointments</h3>
                
                @forelse($appointments ?? [] as $appointment)
                    <div class="border rounded-md p-4 bg-gray-50 flex justify-between items-center mb-3">
                        <div>
                            <p class="font-semibold text-indigo-600">Reason: {{ $appointment->symptoms }}</p>
                            <p class="text-sm text-gray-600">Date: {{ $appointment->consultation_date }} ({{ ucfirst($appointment->time_slot) }})</p>
                            
                            @if($appointment->status === 'Pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mt-2">
                                    Pending Approval
                                </span>
                            @elseif($appointment->status === 'Approved')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-2">
                                    Confirmed Schedule
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 mt-2">
                                    Rejected: {{ $appointment->rejection_reason ?? 'No reason provided' }}
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">You have no scheduled appointments yet. Click above to schedule one.</p>
                @endforelse
            </div>

            <!-- 3. Consultation History and Available Records -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900">
                <h3 class="text-lg font-medium mb-4">Consultation History & Prescriptions</h3>
                <p class="text-sm text-gray-600">Past medical advice, treatment instructions, and prescriptions will appear here after your health center visits.</p>
            </div>

        </div>
    </div>
</x-app-layout>