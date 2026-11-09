<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Staff / Doctor Dashboard - Appointment Requests') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900">
                <h3 class="text-lg font-medium mb-4">Incoming Patient Appointments (Total: {{ isset($appointments) ? $appointments->count() : 0 }})</h3>

                @if(isset($appointments) && $appointments->count() > 0)
                    <div class="space-y-4">
                        @foreach($appointments as $appointment)
                            <div class="border rounded-md p-4 bg-gray-50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                                <div>
                                    <p class="font-semibold text-indigo-600">Patient: {{ $appointment->user->name ?? 'Unknown User' }} ({{ $appointment->user->email ?? 'No Email' }})</p>
                                    <p class="text-sm text-gray-700">Complaint / Reason: <strong>{{ $appointment->symptoms }}</strong></p>
                                    <p class="text-sm text-gray-600">Date: {{ $appointment->consultation_date }} ({{ ucfirst($appointment->time_slot) }})</p>
                                    <p class="text-xs text-gray-500 mt-1">Status: 
                                        <span class="font-semibold {{ $appointment->status === 'Pending' ? 'text-yellow-600' : ($appointment->status === 'Approved' ? 'text-green-600' : 'text-red-600') }}">
                                            {{ $appointment->status }}
                                        </span>
                                        @if($appointment->status === 'Rejected' && $appointment->rejection_reason)
                                            <span class="text-red-500 block">Reason: {{ $appointment->rejection_reason }}</span>
                                        @endif
                                    </p>
                                </div>

                                <!-- Action Forms -->
                                @if($appointment->status === 'Pending')
                                    <div class="flex flex-col sm:flex-row items-center gap-2">
                                        <!-- Approve Form -->
                                        <form method="POST" action="{{ route('appointments.update-status', $appointment->id) }}">
                                            @csrf
                                            <input type="hidden" name="status" value="Approved">
                                            <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded text-xs font-semibold hover:bg-green-500">Approve</button>
                                        </form>

                                        <!-- Reject Form -->
                                        <form method="POST" action="{{ route('appointments.update-status', $appointment->id) }}" class="flex items-center gap-1">
                                            @csrf
                                            <input type="hidden" name="status" value="Rejected">
                                            <input type="text" name="rejection_reason" placeholder="Reason..." class="text-xs border-gray-300 rounded px-2 py-1" required>
                                            <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded text-xs font-semibold hover:bg-red-500">Reject</button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">No appointment requests found in the database.</p>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>