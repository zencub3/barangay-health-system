<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-[#184e37]">
            Patient Dashboard
        </h2>
    </x-slot>

    <style>
        .patient-dashboard {
            background: #f2f7f3;
            min-height: calc(100vh - 130px);
            padding: 35px 20px;
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #22372d;
        }

        .patient-container {
            max-width: 1180px;
            margin: auto;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .patient-card {
            background: #ffffff;
            border: 1px solid #dce9df;
            border-radius: 16px;
            box-shadow: 0 3px 12px rgba(20,70,45,.04);
            padding: 30px;
        }

        .patient-card h3 {
            font-size: 19px;
            font-weight: 650;
            color: #184e37;
            margin-bottom: 12px;
        }

        .patient-card p {
            font-size: 15px;
            color: #61786b;
            line-height: 1.8;
        }

        .patient-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-top: 18px;
            padding: 13px 24px;
            border-radius: 10px;
            background: #216e4f;
            color: #ffffff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            transition: .2s ease;
        }

        .patient-btn:hover {
            background: #175239;
        }

        .appointments-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
            margin-top: 10px;
        }

        .appointment-item {
            border: 1px solid #dce9df;
            border-radius: 14px;
            background: #f8fcf9;
            padding: 18px 20px;
        }

        .appointment-item h4 {
            margin: 0 0 8px 0;
            font-size: 17px;
            color: #184e37;
            font-weight: 650;
        }

        .appointment-item p {
            margin: 4px 0;
            font-size: 14px;
            color: #556d60;
        }

        .status-badge {
            display: inline-block;
            margin-top: 12px;
            padding: 6px 13px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 700;
        }

        .status-pending {
            background: #fff2d6;
            color: #996313;
        }

        .status-approved {
            background: #dcf5e6;
            color: #176445;
        }

        .status-rejected {
            background: #ffe5e5;
            color: #ad3333;
        }

        .empty-state {
            padding: 18px 0 4px;
            color: #819187;
            font-size: 14px;
        }

        .history-box {
            border: 1px dashed #cfe1d5;
            background: #f8fcf9;
            border-radius: 14px;
            padding: 18px 20px;
            margin-top: 14px;
        }

        .history-box p {
            margin: 0;
            font-size: 14px;
            color: #667d71;
        }

        @media (max-width: 768px) {
            .patient-dashboard {
                padding: 24px 14px;
            }

            .patient-card {
                padding: 22px 18px;
            }
        }
    </style>

    @php
        $appointmentList = collect($appointments ?? []);
        $historyList = collect($consultationHistory ?? []);
    @endphp

    <div class="patient-dashboard">
        <div class="patient-container">

            <!-- Book a Consultation -->
            <div class="patient-card">
                <h3>Book a Consultation</h3>
                <p>
                    Choose your preferred date, available time slot, and present
                    your symptoms or reason for visit.
                </p>

                <a href="{{ url('/appointments/create') }}" class="patient-btn">
                    Schedule Appointment
                </a>
            </div>

            <!-- My Appointments -->
            <div class="patient-card">
                <h3>My Appointments</h3>

                @if($appointmentList->count() > 0)
                    <div class="appointments-list">
                        @foreach($appointmentList as $appointment)
                            @php
                                $status = strtolower($appointment->status ?? 'pending');
                            @endphp

                            <div class="appointment-item">
                                <h4>
                                    Reason: {{ $appointment->symptoms ?: 'Not provided' }}
                                </h4>

                                <p>
                                    Date: {{ $appointment->consultation_date }}
                                    ({{ ucfirst($appointment->time_slot ?? 'N/A') }})
                                </p>

                                @if($status === 'approved')
                                    <span class="status-badge status-approved">
                                        Confirmed Schedule
                                    </span>
                                @elseif($status === 'pending')
                                    <span class="status-badge status-pending">
                                        Pending Approval
                                    </span>
                                @elseif($status === 'rejected')
                                    <span class="status-badge status-rejected">
                                        Rejected
                                    </span>

                                    @if(!empty($appointment->rejection_reason))
                                        <p style="margin-top:10px;">
                                            Reason: {{ $appointment->rejection_reason }}
                                        </p>
                                    @endif
                                @else
                                    <span class="status-badge status-pending">
                                        {{ ucfirst($status) }}
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        No appointments yet.
                    </div>
                @endif
            </div>

            
        
    </div>
</x-app-layout>