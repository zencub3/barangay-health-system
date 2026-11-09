
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-[#184e37]">
            Healthcare Personnel
        </h2>
    </x-slot>

    <style>
        .health-dashboard {
            background: #f2f7f3;
            min-height: calc(100vh - 130px);
            padding: 35px 20px;
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #22372d;
        }

        .health-container {
            max-width: 1280px;
            margin: auto;
            display: grid;
            grid-template-columns: 260px minmax(0, 1fr);
            gap: 25px;
            align-items: start;
        }

        .health-sidebar,
        .health-panel {
            background: #ffffff;
            border: 1px solid #dce9df;
            border-radius: 15px;
            box-shadow: 0 3px 12px rgba(20,70,45,.04);
        }

        .health-sidebar {
            padding: 20px;
            min-height: 470px;
            display: flex;
            flex-direction: column;
        }

        .health-menu {
            display: flex;
            flex-direction: column;
            gap: 9px;
        }

        .health-menu button {
            width: 100%;
            text-align: left;
            cursor: pointer;
            border: none;
            border-radius: 9px;
            padding: 13px 16px;
            font-size: 14px;
            font-weight: 600;
            transition: .2s;
        }

        .health-menu button.active {
            background: #216e4f;
            color: #ffffff;
        }

        .health-menu button:not(.active) {
            background: #e8f5ec;
            color: #216e4f;
        }

        .health-menu button:hover {
            opacity: .88;
        }

        .health-session {
            margin-top: auto;
            padding-top: 18px;
            border-top: 1px solid #e2ece5;
        }

        .health-session small {
            display: block;
            color: #789080;
            letter-spacing: 1px;
            font-size: 11px;
            margin-bottom: 6px;
        }

        .health-session strong {
            color: #184e37;
            font-size: 13px;
        }

        .health-panel {
            padding: 28px;
            min-width: 0;
        }

        .health-panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
        }

        .health-panel-header h3 {
            font-size: 20px;
            font-weight: 650;
            color: #184e37;
        }

        .health-count {
            background: #e8f5ec;
            color: #216e4f;
            padding: 7px 14px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }

        .health-table-wrap {
            overflow-x: auto;
        }

        .health-table {
            width: 100%;
            min-width: 720px;
            border-collapse: collapse;
            text-align: left;
        }

        .health-table th {
            padding: 15px;
            background: #f3f9f4;
            color: #55715e;
            font-size: 11px;
            font-weight: 750;
            text-transform: uppercase;
            letter-spacing: .4px;
        }

        .health-table td {
            padding: 18px 15px;
            border-bottom: 1px solid #e5eee7;
            color: #34483b;
            font-size: 13px;
            vertical-align: middle;
        }

        .health-table td strong {
            color: #183e30;
        }

        .health-muted {
            font-size: 12px;
            color: #819187;
            margin-top: 5px;
        }

        .health-status {
            display: inline-block;
            padding: 6px 13px;
            border-radius: 30px;
            font-size: 11px;
            font-weight: 700;
        }

        .status-approved {
            background: #dcf5e6;
            color: #176445;
        }

        .status-pending {
            background: #fff2d6;
            color: #996313;
        }

        .status-rejected {
            background: #ffe5e5;
            color: #ad3333;
        }

        .health-actions {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .health-approve,
        .health-reject {
            border: 0;
            border-radius: 8px;
            padding: 9px 13px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 650;
        }

        .health-approve {
            background: #216e4f;
            color: white;
        }

        .health-reject {
            background: #ffe5e5;
            color: #a12b2b;
        }

        .health-reason {
            width: 150px;
            border: 1px solid #dce9df;
            border-radius: 8px;
            padding: 9px;
            font-size: 12px;
        }

        .health-alert {
            background: #e8f5ec;
            color: #216e4f;
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .health-empty {
            padding: 35px 15px;
            text-align: center;
            color: #819187;
            font-size: 14px;
        }

        @media(max-width: 800px) {
            .health-container {
                grid-template-columns: 1fr;
            }

            .health-sidebar {
                min-height: auto;
            }

            .health-session {
                margin-top: 25px;
            }

            .health-panel {
                padding: 18px;
            }
        }
    </style>

    @php
        $appointmentList = collect($appointments ?? []);
    @endphp

    <div class="health-dashboard">
        <div class="health-container">

            <!-- Sidebar -->
            <aside class="health-sidebar">
                <div class="health-menu">

                    <button
                        type="button"
                        class="active"
                        id="appointmentsTab"
                        onclick="showHealthTab('appointments')"
                    >
                        Appointments
                    </button>

                    <button
                        type="button"
                        id="analyticsTab"
                        onclick="showHealthTab('analytics')"
                    >
                        Analytics
                    </button>

                </div>

                <div class="health-session">
                    <small>SESSION</small>
                    <strong>
                        Welcome, {{ Auth::user()->name }}
                    </strong>
                </div>
            </aside>

            <!-- Main Content -->
            <section class="health-panel">

                @if(session('success'))
                    <div class="health-alert">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Appointments Section -->
                <div id="appointmentsContent">

                    <div class="health-panel-header">
                        <h3>Community Consultation Requests</h3>

                        <span class="health-count">
                            Total: {{ $appointmentList->count() }}
                        </span>
                    </div>

                    <div class="health-table-wrap">
                        <table class="health-table">
                            <thead>
                                <tr>
                                    <th>Patient Name</th>
                                    <th>Preferred Date</th>
                                    <th>Symptoms / Complaint</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($appointmentList as $appointment)
                                    @php
                                        $status = strtolower($appointment->status ?? 'pending');
                                    @endphp

                                    <tr>
                                        <td>
                                            <strong>
                                                {{ $appointment->user->name ?? 'Unknown User' }}
                                            </strong>

                                            <p class="health-muted">
                                                {{ $appointment->user->email ?? 'No Email' }}
                                            </p>
                                        </td>

                                        <td>
                                            {{ $appointment->consultation_date }}

                                            <p class="health-muted">
                                                {{ ucfirst($appointment->time_slot) }}
                                            </p>
                                        </td>

                                        <td>
                                            {{ $appointment->symptoms ?: 'Not provided' }}
                                        </td>

                                        <td>
                                            <span class="health-status
                                                {{ $status === 'approved' ? 'status-approved' :
                                                ($status === 'pending' ? 'status-pending' : 'status-rejected') }}">
                                                {{ ucfirst($status) }}
                                            </span>

                                            @if($status === 'rejected' && $appointment->rejection_reason)
                                                <p class="health-muted">
                                                    Reason: {{ $appointment->rejection_reason }}
                                                </p>
                                            @endif
                                        </td>

                                        <td>
                                            @if($status === 'pending')
                                                <div class="health-actions">

                                                    <!-- Approve -->
                                                    <form
                                                        method="POST"
                                                        action="{{ route('appointments.update-status', $appointment->id) }}"
                                                    >
                                                        @csrf

                                                        <input
                                                            type="hidden"
                                                            name="status"
                                                            value="Approved"
                                                        >

                                                        <button
                                                            type="submit"
                                                            class="health-approve"
                                                        >
                                                            Approve
                                                        </button>
                                                    </form>

                                                    <!-- Reject -->
                                                    <form
                                                        method="POST"
                                                        action="{{ route('appointments.update-status', $appointment->id) }}"
                                                    >
                                                        @csrf

                                                        <input
                                                            type="hidden"
                                                            name="status"
                                                            value="Rejected"
                                                        >

                                                        <input
                                                            type="text"
                                                            name="rejection_reason"
                                                            placeholder="Reason..."
                                                            required
                                                            class="health-reason"
                                                        >

                                                        <button
                                                            type="submit"
                                                            class="health-reject"
                                                        >
                                                            Reject
                                                        </button>
                                                    </form>

                                                </div>
                                            @else
                                                <span class="health-muted">
                                                    Processed
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="health-empty">
                                            No appointment requests found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Analytics Section -->
                <div id="analyticsContent" style="display:none;">

                    <div class="health-panel-header">
                        <h3>Analytics</h3>
                    </div>

                    <p class="health-muted">
                        Appointment summary
                    </p>

                    <div style="margin-top:20px; line-height:2;">
                        <p>
                            Total:
                            {{ $appointmentList->count() }}
                        </p>

                        <p>
                            Pending:
                            {{ $appointmentList->where('status', 'Pending')->count() }}
                        </p>

                        <p>
                            Approved:
                            {{ $appointmentList->where('status', 'Approved')->count() }}
                        </p>

                        <p>
                            Rejected:
                            {{ $appointmentList->where('status', 'Rejected')->count() }}
                        </p>
                    </div>

                </div>

            </section>

        </div>
    </div>

    <script>
        function showHealthTab(tab) {
            const isAppointments = tab === 'appointments';

            document.getElementById('appointmentsContent')
                .style.display = isAppointments ? 'block' : 'none';

            document.getElementById('analyticsContent')
                .style.display = isAppointments ? 'none' : 'block';

            document.getElementById('appointmentsTab')
                .classList.toggle('active', isAppointments);

            document.getElementById('analyticsTab')
                .classList.toggle('active', !isAppointments);
        }
    </script>

</x-app-layout>
