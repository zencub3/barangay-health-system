<x-app-layout>
    <x-slot name="header"></x-slot>

    <div class="py-12" x-data="{ currentTab: 'appointments' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- STICKY SIDEBAR + CONTENT GRID -->
            <div style="display: grid; grid-template-columns: 260px 1fr; gap: 24px; align-items: start;">
                
                <!-- LEFT SIDEBAR -->
                <div style="background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); min-height: 400px; display: flex; flex-direction: column; justify-content: space-between;">
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <!-- Appointments Button -->
                        <button type="button"
                                @click="currentTab = 'appointments'"
                                :style="currentTab === 'appointments' ? 'background-color: #047857; color: #ffffff; font-weight: bold;' : 'background-color: #d1fae5; color: #065f46; font-weight: 500;'"
                                style="width: 100%; text-align: left; padding: 12px 16px; border-radius: 6px; border: none; cursor: pointer; transition: all 0.2s;"
                                @mouseenter="$el.style.backgroundColor='#047857'; $el.style.color='#ffffff';"
                                @mouseleave="if(currentTab !== 'appointments') { $el.style.backgroundColor='#d1fae5'; $el.style.color='#065f46'; }">
                            Appointments
                        </button>

                        <!-- Analytics Button -->
                        <button type="button"
                                @click="currentTab = 'analytics'"
                                :style="currentTab === 'analytics' ? 'background-color: #047857; color: #ffffff; font-weight: bold;' : 'background-color: #d1fae5; color: #065f46; font-weight: 500;'"
                                style="width: 100%; text-align: left; padding: 12px 16px; border-radius: 6px; border: none; cursor: pointer; transition: all 0.2s;"
                                @mouseenter="$el.style.backgroundColor='#047857'; $el.style.color='#ffffff';"
                                @mouseleave="if(currentTab !== 'analytics') { $el.style.backgroundColor='#d1fae5'; $el.style.color='#065f46'; }">
                            Analytics
                        </button>
                    </div>

                    <!-- Welcome User at the Bottom -->
                    <div style="border-top: 1px solid #e5e7eb; padding-top: 16px; margin-top: 24px;">
                        <p style="font-size: 11px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em;">Session</p>
                        <p style="font-size: 13px; font-weight: 600; color: #1f2937; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Welcome, {{ auth()->user()->name }}</p>
                    </div>
                </div>

                <!-- RIGHT CONTENT AREA -->
                <div style="min-width: 0;">

                    <!-- Success Alert -->
                    @if(session('success'))
                        <div style="padding: 16px; margin-bottom: 16px; font-size: 14px; color: #065f46; background-color: #d1fae5; border: 1px solid #a7f3d0; border-radius: 8px;" role="alert">
                            <span style="font-weight: 600;">Success!</span> {{ session('success') }}
                        </div>
                    @endif

                    <!-- TAB 1: APPOINTMENTS MANAGEMENT -->
                    <div x-show="currentTab === 'appointments'" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); border: 1px solid #e5e7eb; padding: 24px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                            <h3 style="font-size: 18px; font-weight: 500; color: #111827;">Community Consultation Requests</h3>
                            <span style="padding: 4px 12px; font-size: 12px; font-weight: 600; color: #4b5563; background-color: #f3f4f6; border-radius: 9999px;">
                                Total: {{ isset($appointments) ? $appointments->count() : 0 }}
                            </span>
                        </div>

                        @if(isset($appointments) && $appointments->count() > 0)
                            <div style="overflow-x-auto; border: 1px solid #e5e7eb; border-radius: 8px;">
                                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                                    <thead style="background-color: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                        <tr>
                                            <th style="padding: 12px 24px; font-size: 11px; font-weight: 500; color: #6b7280; text-transform: uppercase;">Patient Name</th>
                                            <th style="padding: 12px 24px; font-size: 11px; font-weight: 500; color: #6b7280; text-transform: uppercase;">Preferred Date</th>
                                            <th style="padding: 12px 24px; font-size: 11px; font-weight: 500; color: #6b7280; text-transform: uppercase;">Symptoms / Complaint</th>
                                            <th style="padding: 12px 24px; font-size: 11px; font-weight: 500; color: #6b7280; text-transform: uppercase;">Status</th>
                                            <th style="padding: 12px 24px; font-size: 11px; font-weight: 500; color: #6b7280; text-transform: uppercase; text-align: right;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody style="background-color: #ffffff;">
                                        @foreach($appointments as $appointment)
                                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                                <td style="padding: 16px 24px; font-size: 14px; font-weight: 500; color: #111827;">
                                                    {{ $appointment->patient->name ?? 'Unknown' }}
                                                    <div style="font-size: 12px; color: #6b7280;">{{ $appointment->patient->email ?? '' }}</div>
                                                </td>
                                                <td style="padding: 16px 24px; font-size: 14px; color: #4b5563;">
                                                    {{ $appointment->consultation_date }}
                                                    <div style="font-size: 12px; color: #9ca3af; text-transform: capitalize;">{{ $appointment->time_slot }}</div>
                                                </td>
                                                <td style="padding: 16px 24px; font-size: 14px; color: #374151; max-width: 200px;">
                                                    {{ $appointment->symptoms }}
                                                    @if($appointment->other_service)
                                                        <div style="font-size: 12px; color: #047857; font-weight: 500; margin-top: 4px;">
                                                            Details: {{ $appointment->other_service }}
                                                        </div>
                                                    @endif
                                                </td>
                                                <td style="padding: 16px 24px; font-size: 14px;">
                                                    @php
                                                        $statusClass = match(strtolower($appointment->status)) {
                                                            'approved' => 'background-color: #d1fae5; color: #065f46;',
                                                            'rejected' => 'background-color: #fee2e2; color: #991b1b;',
                                                            default => 'background-color: #fef3c7; color: #92400e;',
                                                        };
                                                    @endphp
                                                    <span style="padding: 4px 10px; font-size: 12px; font-weight: 600; border-radius: 9999px; {{ $statusClass }}">
                                                        {{ ucfirst($appointment->status) }}
                                                    </span>
                                                    @if(strtolower($appointment->status) === 'rejected' && $appointment->rejection_reason)
                                                        <div style="font-size: 11px; color: #ef4444; margin-top: 4px;">Reason: {{ $appointment->rejection_reason }}</div>
                                                    @endif
                                                </td>
                                                <td style="padding: 16px 24px; text-align: right; font-size: 14px;">
                                                    @if(strtolower($appointment->status) === 'pending')
                                                        <form action="{{ route('staff.appointment.update', $appointment->id) }}" method="POST" style="display: inline;">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="approved">
                                                            <button type="submit" style="padding: 6px 12px; background-color: #16a34a; color: #ffffff; border: none; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; text-transform: uppercase;">
                                                                Approve
                                                            </button>
                                                        </form>

                                                        <form action="{{ route('staff.appointment.update', $appointment->id) }}" method="POST" style="display: inline; margin-left: 4px;" onsubmit="let reason = prompt('Please provide a reason for rejection:'); if(!reason) return false; this.querySelector('input[name=rejection_reason]').value = reason;">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="rejected">
                                                            <input type="hidden" name="rejection_reason" value="">
                                                            <button type="submit" style="padding: 6px 12px; background-color: #dc2626; color: #ffffff; border: none; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; text-transform: uppercase;">
                                                                Reject
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span style="font-size: 12px; color: #9ca3af; font-weight: 500;">Processed</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div style="text-align: center; padding: 48px; background-color: #f9fafb; border-radius: 8px; border: 2px dashed #e5e7eb;">
                                <p style="font-size: 14px; color: #6b7280;">No consultation requests found in the system.</p>
                            </div>
                        @endif
                    </div>

                    <!-- TAB 2: ANNUAL HEALTH ANALYTICS -->
                    <div x-show="currentTab === 'analytics'" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); border: 1px solid #e5e7eb; padding: 24px; display: none;">
                        <div style="margin-bottom: 24px;">
                            <h3 style="font-size: 18px; font-weight: 500; color: #111827;">Annual Health & Sickness Report ({{ $currentYear ?? date('Y') }})</h3>
                            <p style="font-size: 12px; color: #6b7280; margin-top: 4px;">Percentage breakdown based on total reports ({{ $totalYearCount ?? 0 }} cases)</p>
                        </div>

                        @if(isset($symptomStats) && count($symptomStats) > 0)
                            <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 24px;">
                                @foreach($symptomStats as $stat)
                                    @php
                                        $colorHex = match($loop->index % 4) {
                                            0 => '#4f46e5',
                                            1 => '#dc2626',
                                            2 => '#2563eb',
                                            default => '#16a34a',
                                        };
                                    @endphp
                                    <div style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center;">
                                        <div style="position: relative; width: 112px; height: 112px; border-radius: 9999px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px; background: conic-gradient({{ $colorHex }} {{ $stat['percentage'] }}%, #e5e7eb 0%);">
                                            <div style="position: absolute; width: 88px; height: 88px; background-color: #ffffff; border-radius: 9999px; display: flex; flex-direction: column; align-items: center; justify-content: center; box-shadow: inset 0 2px 4px 0 rgba(0,0,0,0.06);">
                                                <span style="font-size: 20px; font-weight: bold; color: {{ $colorHex }};">{{ $stat['percentage'] }}%</span>
                                            </div>
                                        </div>
                                        <h4 style="font-weight: 600; color: #1f2937; font-size: 14px; margin-bottom: 4px; width: 100%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $stat['label'] }}">{{ $stat['label'] }}</h4>
                                        <p style="font-size: 12px; color: #6b7280;">Reported Cases: <span style="font-weight: 500; color: #374151;">{{ $stat['count'] }}</span></p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div style="text-align: center; padding: 48px; background-color: #f9fafb; border-radius: 8px; border: 2px dashed #e5e7eb;">
                                <p style="font-size: 14px; color: #6b7280;">No annual health statistics available for {{ $currentYear ?? date('Y') }} yet.</p>
                            </div>
                        @endif
                    </div>

                </div>

            </div>
        </div>
    </div>
</x-app-layout>