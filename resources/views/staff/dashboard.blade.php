<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-[#184e37]">
            Healthcare Personnel
        </h2>
    </x-slot>

    @php
        $appointmentList = collect($appointments ?? []);
        $pendingCount = $appointmentList->where('status', 'Pending')->count();
        $approvedCount = $appointmentList->where('status', 'Approved')->count();
        $rejectedCount = $appointmentList->where('status', 'Rejected')->count();
        $totalCount = $appointmentList->count();
    @endphp

    <style>
        .staff-dashboard {
            background: #f2f7f3;
            min-height: calc(100vh - 120px);
            padding: 34px 20px;
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #22372d;
        }

        .staff-wrapper {
            max-width: 1250px;
            margin: auto;
            display: grid;
            grid-template-columns: 260px minmax(0, 1fr);
            gap: 28px;
            align-items: start;
        }

        .staff-sidebar,
        .staff-main-card {
            background: #ffffff;
            border: 1px solid #dce9df;
            border-radius: 18px;
            box-shadow: 0 4px 14px rgba(20,70,45,.04);
        }

        .staff-sidebar {
            padding: 24px;
            min-height: 590px;
            display: flex;
            flex-direction: column;
        }

        .staff-nav {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .staff-tab-btn {
            border: none;
            border-radius: 12px;
            padding: 15px 18px;
            text-align: left;
            font-size: 15px;
            font-weight: 700;
            background: #e8f5ec;
            color: #216e4f;
            cursor: pointer;
            transition: .2s ease;
        }

        .staff-tab-btn:hover {
            background: #dff0e5;
        }

        .staff-tab-btn.active {
            background: #0f7b4f;
            color: #ffffff;
        }

        .staff-session {
            margin-top: auto;
            border-top: 1px solid #dce9df;
            padding-top: 18px;
        }

        .staff-session small {
            display: block;
            color: #7a8d82;
            letter-spacing: 1px;
            font-size: 12px;
            margin-bottom: 8px;
        }

        .staff-session strong {
            color: #184e37;
            font-size: 14px;
        }

        .staff-main-card {
            padding: 30px;
        }

        .staff-section {
            display: none;
        }

        .staff-section.active {
            display: block;
        }

        .staff-header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 22px;
        }

        .staff-title {
            margin: 0;
            font-size: 20px;
            font-weight: 750;
            color: #184e37;
        }

        .staff-subtitle {
            color: #7a8d82;
            font-size: 14px;
            margin-top: 6px;
        }

        .staff-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 14px;
            border-radius: 999px;
            background: #eef7f1;
            color: #216e4f;
            font-size: 12px;
            font-weight: 700;
        }

        .staff-alert-success {
            background: #e8f5ec;
            border: 1px solid #bfe2cb;
            color: #216e4f;
            padding: 14px 16px;
            border-radius: 12px;
            font-size: 14px;
            margin-bottom: 18px;
        }

        .staff-table-wrap {
            width: 100%;
            overflow-x: auto;
        }

        .staff-table {
            width: 100%;
            min-width: 900px;
            border-collapse: collapse;
        }

        .staff-table thead th {
            text-align: left;
            background: #f3f9f4;
            color: #597265;
            font-size: 12px;
            font-weight: 700;
            padding: 15px 16px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .staff-table tbody td {
            padding: 18px 16px;
            border-bottom: 1px solid #e6eee8;
            vertical-align: top;
            font-size: 14px;
            color: #3f5649;
        }

        .staff-table tbody tr:hover {
            background: #fafdfb;
        }

        .staff-name {
            font-weight: 700;
            color: #184e37;
            margin-bottom: 4px;
        }

        .staff-email {
            font-size: 13px;
            color: #7a8d82;
        }

        .staff-reason {
            font-weight: 600;
            color: #27493a;
        }

        .staff-date {
            font-weight: 600;
            color: #27493a;
        }

        .staff-slot {
            display: block;
            font-size: 12px;
            color: #7a8d82;
            margin-top: 4px;
        }

        .staff-status {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .staff-status.pending {
            background: #fff2d6;
            color: #9a6a11;
        }

        .staff-status.approved {
            background: #dcf5e6;
            color: #176445;
        }

        .staff-status.rejected {
            background: #ffe5e5;
            color: #ad3333;
        }

        .staff-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
            min-width: 180px;
        }

        .staff-inline-form {
            display: flex;
            gap: 8px;
            align-items: stretch;
            flex-wrap: wrap;
        }

        .staff-btn {
            border: none;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: .2s ease;
        }

        .staff-btn.approve {
            background: #0f7b4f;
            color: #fff;
        }

        .staff-btn.approve:hover {
            background: #0b673f;
        }

        .staff-btn.reject {
            background: #c0392b;
            color: #fff;
        }

        .staff-btn.reject:hover {
            background: #a93226;
        }

        .staff-reason-input {
            flex: 1;
            min-width: 110px;
            border: 1px solid #cfe1d5;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 12px;
            outline: none;
        }

        .staff-reason-input:focus {
            border-color: #74b28f;
            box-shadow: 0 0 0 3px rgba(116,178,143,.15);
        }

        .staff-processed {
            font-size: 13px;
            font-weight: 700;
            color: #7a8d82;
        }

        .staff-rejection-note {
            margin-top: 6px;
            color: #b23a3a;
            font-size: 12px;
        }

        .analytics-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 26px;
        }

        .analytics-card {
            background: #f8fcf9;
            border: 1px solid #dce9df;
            border-radius: 14px;
            padding: 18px;
        }

        .analytics-card small {
            display: block;
            color: #7a8d82;
            font-size: 12px;
            margin-bottom: 8px;
        }

        .analytics-card strong {
            font-size: 28px;
            color: #184e37;
            font-weight: 800;
        }

        .chart-block {
            background: #ffffff;
            border: 1px solid #e1ebe4;
            border-radius: 14px;
            padding: 18px;
        }

        .chart-block h4 {
            margin: 0 0 18px 0;
            color: #184e37;
            font-size: 17px;
            font-weight: 700;
        }

        .chart-row {
            margin-bottom: 16px;
        }

        .chart-row:last-child {
            margin-bottom: 0;
        }

        .chart-label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: #3f5649;
            margin-bottom: 8px;
        }

        .chart-bar {
            width: 100%;
            height: 12px;
            border-radius: 999px;
            background: #e7f1ea;
            overflow: hidden;
        }

        .chart-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #1e7a52, #57a679);
        }

        .empty-state {
            padding: 24px;
            text-align: center;
            color: #7a8d82;
            font-size: 14px;
            background: #f9fcfa;
            border: 1px dashed #d1e1d6;
            border-radius: 14px;
        }

        @media (max-width: 1024px) {
            .staff-wrapper {
                grid-template-columns: 1fr;
            }

            .staff-sidebar {
                min-height: auto;
            }

            .analytics-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .staff-dashboard {
                padding: 20px 14px;
            }

            .staff-main-card,
            .staff-sidebar {
                padding: 18px;
            }

            .analytics-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="staff-dashboard">
        <div class="staff-wrapper">

            <!-- Sidebar -->
            <aside class="staff-sidebar">
                <div class="staff-nav">
                    <button class="staff-tab-btn active" onclick="showStaffTab('appointments', this)">
                        Appointments
                    </button>
                    <button class="staff-tab-btn" onclick="showStaffTab('analytics', this)">
                        Analytics
                    </button>
                </div>

                <div class="staff-session">
                    <small>SESSION</small>
                    <strong>Welcome, {{ Auth::user()->name }}</strong>
                </div>
            </aside>

            <!-- Main Content -->
            <section class="staff-main-card">

                @if(session('success'))
                    <div class="staff-alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Appointments -->
                <div id="staff-appointments" class="staff-section active">
                    <div class="staff-header-row">
                        <div>
                            <h3 class="staff-title">Appointment Requests</h3>
                            <div class="staff-subtitle">
                                Review, approve, or reject patient consultation requests.
                            </div>
                        </div>

                        <span class="staff-pill">Total: {{ $totalCount }}</span>
                    </div>

                    @if($appointmentList->count() > 0)
                        <div class="staff-table-wrap">
                            <table class="staff-table">
                                <thead>
                                    <tr>
                                        <th>Patient</th>
                                        <th>Complaint / Reason</th>
                                        <th>Schedule</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($appointmentList as $appointment)
                                        <tr>
                                            <td>
                                                <div class="staff-name">
                                                    {{ $appointment->user->name ?? 'Unknown User' }}
                                                </div>
                                                <div class="staff-email">
                                                    {{ $appointment->user->email ?? 'No Email' }}
                                                </div>
                                            </td>

                                            <td>
                                                <div class="staff-reason">
                                                    {{ $appointment->symptoms ?: 'Not provided' }}
                                                </div>
                                                @if(!empty($appointment->other_service))
                                                    <div class="staff-email" style="margin-top:4px;">
                                                        Other service: {{ $appointment->other_service }}
                                                    </div>
                                                @endif
                                            </td>

                                            <td>
                                                <div class="staff-date">
                                                    {{ $appointment->consultation_date }}
                                                </div>
                                                <span class="staff-slot">
                                                    {{ ucfirst($appointment->time_slot ?? 'N/A') }}
                                                </span>
                                            </td>

                                            <td>
                                                <span class="staff-status {{ strtolower($appointment->status) }}">
                                                    {{ $appointment->status }}
                                                </span>

                                                @if($appointment->status === 'Rejected' && $appointment->rejection_reason)
                                                    <div class="staff-rejection-note">
                                                        Reason: {{ $appointment->rejection_reason }}
                                                    </div>
                                                @endif
                                            </td>

                                            <td>
                                                @if($appointment->status === 'Pending')
                                                    <div class="staff-actions">
                                                        <!-- Approve -->
                                                        <form method="POST" action="{{ route('appointments.update-status', $appointment->id) }}">
                                                            @csrf
                                                            <input type="hidden" name="status" value="Approved">
                                                            <button type="submit" class="staff-btn approve">
                                                                Approve
                                                            </button>
                                                        </form>

                                                        <!-- Reject -->
                                                        <form method="POST" action="{{ route('appointments.update-status', $appointment->id) }}">
                                                            @csrf
                                                            <div class="staff-inline-form">
                                                                <input
                                                                    type="hidden"
                                                                    name="status"
                                                                    value="Rejected"
                                                                >
                                                                <input
                                                                    type="text"
                                                                    name="rejection_reason"
                                                                    class="staff-reason-input"
                                                                    placeholder="Rejection reason"
                                                                    required
                                                                >
                                                                <button type="submit" class="staff-btn reject">
                                                                    Reject
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                @else
                                                    <div class="staff-processed">
                                                        Processed
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            No appointment requests found.
                        </div>
                    @endif
                </div>

                <!-- Analytics -->
                <div id="staff-analytics" class="staff-section">
                    <div class="staff-header-row">
                        <div>
                            <h3 class="staff-title">Analytics</h3>
                            <div class="staff-subtitle">
                                Appointment summary and consultation statistics.
                            </div>
                        </div>

                        <span class="staff-pill">Year: {{ $currentYear ?? now()->year }}</span>
                    </div>

                    <div class="analytics-grid">
                        <div class="analytics-card">
                            <small>Total Appointments</small>
                            <strong>{{ $totalCount }}</strong>
                        </div>

                        <div class="analytics-card">
                            <small>Pending</small>
                            <strong>{{ $pendingCount }}</strong>
                        </div>

                        <div class="analytics-card">
                            <small>Approved</small>
                            <strong>{{ $approvedCount }}</strong>
                        </div>

                        <div class="analytics-card">
                            <small>Rejected</small>
                            <strong>{{ $rejectedCount }}</strong>
                        </div>
                    </div>

                    <div class="chart-block">
                        <h4>Health Analytics & Statistics</h4>

                        @if(!empty($symptomStats) && count($symptomStats) > 0)
                            @foreach($symptomStats as $stat)
                                <div class="chart-row">
                                    <div class="chart-label">
                                        <span>{{ $stat['label'] }}</span>
                                        <span>{{ $stat['count'] }} visits ({{ $stat['percentage'] }}%)</span>
                                    </div>
                                    <div class="chart-bar">
                                        <div class="chart-fill" style="width: {{ $stat['percentage'] }}%;"></div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state">
                                No approved consultation statistics available yet.
                            </div>
                        @endif
                    </div>
                </div>

            </section>
        </div>
    </div>

    <script>
        function showStaffTab(tab, button) {
            document.querySelectorAll('.staff-section').forEach(section => {
                section.classList.remove('active');
            });

            document.querySelectorAll('.staff-tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });

            document.getElementById('staff-' + tab).classList.add('active');
            button.classList.add('active');
        }
    </script>
</x-app-layout>