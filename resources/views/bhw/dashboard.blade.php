
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-[#184e37]">
            Barangay Health Worker (BHW) Dashboard
        </h2>
    </x-slot>

    <style>
        .bhw-page {
            min-height: 85vh;
            background: #f2f7f3;
            padding: 30px 20px;
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #22372d;
        }

        .bhw-container {
            max-width: 1200px;
            margin: auto;
        }

        .bhw-welcome {
            padding: 26px 30px;
            border-radius: 15px;
            background: linear-gradient(120deg,#184e37,#287453);
            color: white;
            margin-bottom: 22px;
        }

        .bhw-welcome h2 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 7px;
        }

        .bhw-welcome p {
            color: #dceee3;
            font-size: 14px;
        }

        .bhw-card {
            padding: 26px;
            border-radius: 15px;
            border: 1px solid #dce9df;
            background: white;
            margin-bottom: 22px;
            box-shadow: 0 3px 12px rgba(20,70,45,.04);
        }

        .bhw-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 20px;
        }

        .bhw-heading h3 {
            font-size: 18px;
            font-weight: 700;
            color: #184e37;
        }

        .bhw-count {
            padding: 7px 13px;
            border-radius: 25px;
            color: #216e4f;
            background: #e8f5ec;
            font-size: 12px;
            font-weight: 700;
        }

        .bhw-note {
            margin: -5px 0 18px;
            font-size: 13px;
            line-height: 1.6;
            color: #758b7b;
        }

        .bhw-table-wrap {
            overflow-x: auto;
            width: 100%;
        }

        .bhw-table {
            width: 100%;
            min-width: 690px;
            border-collapse: collapse;
            text-align: left;
        }

        .bhw-table th {
            background: #f3f9f4;
            padding: 14px;
            color: #55715e;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .bhw-table td {
            padding: 16px 14px;
            border-bottom: 1px solid #e6eee8;
            font-size: 13px;
            vertical-align: middle;
            color: #40584a;
        }

        .bhw-table strong {
            color: #184e37;
        }

        .bhw-table tbody tr:hover {
            background: #fafdfa;
        }

        .bhw-status {
            display: inline-block;
            padding: 6px 11px;
            border-radius: 25px;
            font-size: 11px;
            font-weight: 700;
        }

        .bhw-approved,
        .bhw-attended {
            background: #dcf5e6;
            color: #176445;
        }

        .bhw-pending,
        .bhw-unconfirmed {
            background: #fff2d6;
            color: #996313;
        }

        .bhw-rejected,
        .bhw-no-show {
            background: #ffe5e5;
            color: #ad3333;
        }

        .bhw-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .bhw-btn {
            border: 0;
            padding: 9px 13px;
            border-radius: 9px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 700;
        }

        .bhw-btn-green {
            background: #216e4f;
            color: white;
        }

        .bhw-btn-red {
            background: #ffe5e5;
            color: #a12b2b;
        }

        .bhw-tabs {
            display: flex;
            gap: 9px;
            flex-wrap: wrap;
            margin-bottom: 19px;
        }

        .bhw-tab {
            border: 1px solid #cfe1d5;
            color: #216e4f;
            background: white;
            padding: 10px 16px;
            border-radius: 9px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
        }

        .bhw-tab.active {
            background: #216e4f;
            border-color: #216e4f;
            color: white;
        }

        .bhw-empty {
            padding: 22px;
            background: #f8fcf9;
            border: 1px dashed #cfe1d5;
            border-radius: 10px;
            color: #708476;
            font-size: 13px;
        }

        .bhw-alert {
            margin-bottom: 20px;
            padding: 14px 18px;
            border-radius: 10px;
            color: #216e4f;
            background: #e8f5ec;
            font-size: 13px;
        }

        .bhw-analytics-row {
            margin-bottom: 17px;
        }

        .bhw-analytics-label {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 9px;
            font-size: 13px;
        }

        .bhw-bar {
            height: 10px;
            border-radius: 20px;
            background: #e8f5ec;
            overflow: hidden;
        }

        .bhw-bar-fill {
            height: 100%;
            background: #287453;
            border-radius: 20px;
        }

        @media(max-width:768px) {
            .bhw-page { padding: 20px 14px; }
            .bhw-card { padding: 18px; }
            .bhw-welcome { padding: 22px; }
        }
    </style>

    <div class="bhw-page">
        <div class="bhw-container">

            <div class="bhw-welcome">
                <h2>Welcome, {{ Auth::user()->name }}</h2>
                <p>
                    Verify barangay residents, monitor appointments,
                    and confirm patient attendance.
                </p>
            </div>

            @if(session('success'))
                <div class="bhw-alert">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bhw-alert"
                     style="background:#ffe5e5;color:#ad3333;">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Patient Verification -->
            <section class="bhw-card">

                <div class="bhw-heading">
                    <h3>Patient Registration Verification</h3>
                    <span class="bhw-count">
                        Pending: {{ $pendingResidents->count() }}
                    </span>
                </div>

                <p class="bhw-note">
                    Review and verify residency against barangay
                    records before approving a patient account.
                </p>

                @if($pendingResidents->isEmpty())
                    <div class="bhw-empty">
                        No pending patient registrations.
                    </div>
                @else
                    <div class="bhw-table-wrap">
                        <table class="bhw-table">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Email</th>
                                    <th>Registered</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($pendingResidents as $resident)
                                    <tr>
                                        <td>
                                            <strong>{{ $resident->name }}</strong>
                                        </td>

                                        <td>{{ $resident->email }}</td>

                                        <td>
                                            {{ $resident->created_at?->format('M d, Y') ?? 'N/A' }}
                                        </td>

                                        <td>
                                            <div class="bhw-actions">

                                                <form method="POST"
                                                      action="{{ route('bhw.users.verify', $resident->id) }}"
                                                      onsubmit="return confirm('Confirm this person is a barangay resident?');">
                                                    @csrf
                                                    @method('PATCH')

                                                    <input type="hidden"
                                                           name="status"
                                                           value="approved">

                                                    <button type="submit"
                                                            class="bhw-btn bhw-btn-green">
                                                        Verify
                                                    </button>
                                                </form>

                                                <form method="POST"
                                                      action="{{ route('bhw.users.verify', $resident->id) }}"
                                                      onsubmit="return confirm('Reject this residency registration?');">
                                                    @csrf
                                                    @method('PATCH')

                                                    <input type="hidden"
                                                           name="status"
                                                           value="rejected">

                                                    <button type="submit"
                                                            class="bhw-btn bhw-btn-red">
                                                        Reject
                                                    </button>
                                                </form>

                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            <!-- Appointment Monitoring -->
            <section class="bhw-card">

                <div class="bhw-heading">
                    <h3>Appointments Monitor</h3>
                    <span class="bhw-count">
                        Total: {{ $appointments->count() }}
                    </span>
                </div>

                <p class="bhw-note">
                    View approved, pending, and rejected schedules.
                    Only Healthcare Personnel can approve or reject
                    medical appointments.
                </p>

                <div class="bhw-tabs">
                    <button type="button"
                            id="bhwUpcomingTab"
                            class="bhw-tab active"
                            onclick="switchBhwTab('upcoming')">
                        Upcoming ({{ $upcoming->count() }})
                    </button>

                    <button type="button"
                            id="bhwHistoryTab"
                            class="bhw-tab"
                            onclick="switchBhwTab('history')">
                        History ({{ $past->count() }})
                    </button>
                </div>

                <div id="bhwUpcomingSection">
                    <div class="bhw-table-wrap">
                        <table class="bhw-table">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Date</th>
                                    <th>Time Slot</th>
                                    <th>Status</th>
                                    <th>Attendance</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($upcoming as $appointment)
                                    @php
                                        $status = strtolower($appointment->status ?? 'pending');
                                        $attendance = $appointment->attendance_status ?? 'unconfirmed';
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>
                                                {{ $appointment->user->name ?? 'Unknown' }}
                                            </strong>
                                        </td>

                                        <td>
                                            {{ $appointment->consultation_date }}
                                        </td>

                                        <td>
                                            {{ ucfirst($appointment->time_slot ?? 'N/A') }}
                                        </td>

                                        <td>
                                            <span class="bhw-status
                                                {{ $status === 'approved' ? 'bhw-approved' :
                                                ($status === 'rejected' ? 'bhw-rejected' : 'bhw-pending') }}">
                                                {{ ucfirst($status) }}
                                            </span>
                                        </td>

                                        <td>
                                            <span class="bhw-status
                                                {{ $attendance === 'attended' ? 'bhw-attended' :
                                                ($attendance === 'no_show' ? 'bhw-no-show' : 'bhw-unconfirmed') }}">
                                                {{ $attendance === 'no_show' ? 'No-show' :
                                                   ucfirst($attendance) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">
                                            No upcoming appointments.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="bhwHistorySection"
                     style="display:none;">
                    <div class="bhw-table-wrap">
                        <table class="bhw-table">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Date</th>
                                    <th>Time Slot</th>
                                    <th>Status</th>
                                    <th>Attendance</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($past as $appointment)
                                    @php
                                        $status = strtolower($appointment->status ?? 'pending');
                                        $attendance = $appointment->attendance_status ?? 'unconfirmed';
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>
                                                {{ $appointment->user->name ?? 'Unknown' }}
                                            </strong>
                                        </td>

                                        <td>
                                            {{ $appointment->consultation_date }}
                                        </td>

                                        <td>
                                            {{ ucfirst($appointment->time_slot ?? 'N/A') }}
                                        </td>

                                        <td>
                                            <span class="bhw-status
                                                {{ $status === 'approved' ? 'bhw-approved' :
                                                ($status === 'rejected' ? 'bhw-rejected' : 'bhw-pending') }}">
                                                {{ ucfirst($status) }}
                                            </span>
                                        </td>

                                        <td>
                                            <span class="bhw-status
                                                {{ $attendance === 'attended' ? 'bhw-attended' :
                                                ($attendance === 'no_show' ? 'bhw-no-show' : 'bhw-unconfirmed') }}">
                                                {{ $attendance === 'no_show' ? 'No-show' :
                                                   ucfirst($attendance) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">
                                            No past appointments.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Attendance Confirmation -->
            <section class="bhw-card">

                <div class="bhw-heading">
                    <h3>Attendance Confirmation</h3>
                    <span class="bhw-count">
                        To Review: {{ $attendanceQueue->count() }}
                    </span>
                </div>

                <p class="bhw-note">
                    Confirm attendance for approved appointments
                    scheduled today or earlier. Future visits
                    cannot be marked as attended.
                    Mark No-show only after confirming
                    the patient did not attend.
                </p>

                @if($attendanceQueue->isEmpty())
                    <div class="bhw-empty">
                        No appointments currently awaiting
                        attendance confirmation.
                    </div>
                @else
                    <div class="bhw-table-wrap">
                        <table class="bhw-table">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Appointment Date</th>
                                    <th>Time Slot</th>
                                    <th>Attendance Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($attendanceQueue as $appointment)
                                    <tr>
                                        <td>
                                            <strong>
                                                {{ $appointment->user->name ?? 'Unknown' }}
                                            </strong>
                                        </td>

                                        <td>
                                            {{ $appointment->consultation_date }}
                                        </td>

                                        <td>
                                            {{ ucfirst($appointment->time_slot ?? 'N/A') }}
                                        </td>

                                        <td>
                                            <div class="bhw-actions">

                                                <form method="POST"
                                                      action="{{ route('bhw.appointments.attendance', $appointment->id) }}"
                                                      onsubmit="return confirm('Confirm that this patient attended the appointment?');">
                                                    @csrf
                                                    @method('PATCH')

                                                    <input type="hidden"
                                                           name="attendance_status"
                                                           value="attended">

                                                    <button type="submit"
                                                            class="bhw-btn bhw-btn-green">
                                                        Attended
                                                    </button>
                                                </form>

                                                <form method="POST"
                                                      action="{{ route('bhw.appointments.attendance', $appointment->id) }}"
                                                      onsubmit="return confirm('Confirm that this patient did not attend?');">
                                                    @csrf
                                                    @method('PATCH')

                                                    <input type="hidden"
                                                           name="attendance_status"
                                                           value="no_show">

                                                    <button type="submit"
                                                            class="bhw-btn bhw-btn-red">
                                                        No-show
                                                    </button>
                                                </form>

                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            <!-- Attendance-based Analytics -->
            <section class="bhw-card">

                <div class="bhw-heading">
                    <h3>
                        Health Analytics & Statistics
                        ({{ $currentYear }})
                    </h3>

                    <span class="bhw-count">
                        Confirmed Attended: {{ $totalYearCount }}
                    </span>
                </div>

                <p class="bhw-note">
                    Only approved appointments marked as Attended
                    by a BHW are counted. Unconfirmed appointments,
                    no-shows, and future schedules are excluded.
                </p>

                @forelse($symptomStats as $stat)
                    <div class="bhw-analytics-row">

                        <div class="bhw-analytics-label">
                            <strong>{{ $stat['label'] }}</strong>
                            <span>
                                {{ $stat['count'] }}
                                attended ({{ $stat['percentage'] }}%)
                            </span>
                        </div>

                        <div class="bhw-bar">
                            <div class="bhw-bar-fill"
                                 style="width: {{ $stat['percentage'] }}%;">
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bhw-empty">
                        No confirmed attended appointments
                        recorded for this year.
                    </div>
                @endforelse
            </section>

        </div>
    </div>

    <script>
        function switchBhwTab(tab) {
            const upcoming = document.getElementById(
                'bhwUpcomingSection'
            );

            const history = document.getElementById(
                'bhwHistorySection'
            );

            const upcomingBtn = document.getElementById(
                'bhwUpcomingTab'
            );

            const historyBtn = document.getElementById(
                'bhwHistoryTab'
            );

            const isUpcoming = tab === 'upcoming';

            upcoming.style.display = isUpcoming ? 'block' : 'none';
            history.style.display = isUpcoming ? 'none' : 'block';

            upcomingBtn.classList.toggle(
                'active', isUpcoming
            );

            historyBtn.classList.toggle(
                'active', !isUpcoming
            );
        }
    </script>
</x-app-layout>
