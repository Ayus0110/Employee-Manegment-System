@extends('layouts.app')

@section('title', 'Attendance Management')
@section('page_title', 'Mark Employee Attendance')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/attendance-admin.css') }}">
@endsection

@section('content')
    @php
        $presentCount = $attendanceStats['present'] ?? 0;
        $absentCount = $attendanceStats['absent'] ?? 0;
        $employeeCount = $employees->count();
        $unmarkedCount = max($employeeCount - ($presentCount + $absentCount), 0);
    @endphp

    <section class="attendance-shell">
        <div class="attendance-hero">
            <div class="hero-copy">
                <p class="eyebrow">Attendance Desk</p>
                <h3>Run daily attendance from one clean workspace.</h3>
                <p>Mark attendance for employees, review the selected day's status, and move to monthly reports without leaving the EMS dashboard.</p>
            </div>
            <div class="hero-side">
                <div class="hero-chip">
                    <span class="hero-chip-label">Selected Date</span>
                    <strong>{{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}</strong>
                </div>
                
                
            
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success attendance-alert">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger attendance-alert">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger attendance-alert">{{ $errors->first() }}</div>
        @endif

        <div class="attendance-stats-grid">
            <article class="metric-card metric-present">
                <div class="metric-icon"><i class="bi bi-person-check-fill"></i></div>
                <div>
                    <span class="metric-label">Present</span>
                    <h4>{{ $presentCount }}</h4>
                    <p>Marked available today</p>
                </div>
            </article>
            <article class="metric-card metric-absent">
                <div class="metric-icon"><i class="bi bi-person-x-fill"></i></div>
                <div>
                    <span class="metric-label">Absent</span>
                    <h4>{{ $absentCount }}</h4>
                    <p>Not available today</p>
                </div>
            </article>
            <article class="metric-card metric-neutral">
                <div class="metric-icon"><i class="bi bi-clock-history"></i></div>
                <div>
                    <span class="metric-label">Unmarked</span>
                    <h4>{{ $unmarkedCount }}</h4>
                    <p>Still waiting for status</p>
                </div>
            </article>
            <article class="metric-card metric-team">
                <div class="metric-icon"><i class="bi bi-people-fill"></i></div>
                <div>
                    <span class="metric-label">Employees</span>
                    <h4>{{ $employeeCount }}</h4>
                    <p>Total active roster</p>
                </div>
            </article>
        </div>

        <div class="content-grid">
            <section class="panel-card form-panel">
                <div class="panel-head">
                    <div>
                        <p class="panel-kicker">Quick Marking</p>
                        <h4>Mark employee attendance</h4>
                    </div>
                    <span class="panel-note">Submitting again updates the same employee record for the chosen date.</span>
                </div>

                <form method="POST" action="{{ route('attendance.store') }}" class="attendance-form">
                    @csrf

                    <div class="grid-2">
                        <div class="form-floating">
                            <input type="date" name="date" id="attendanceDate" class="form-control" value="{{ old('date', $selectedDate) }}" required>
                            <label for="attendanceDate">Attendance Date</label>
                        </div>

                        <div class="form-floating">
                            <select name="user_id" id="employeeEmail" class="form-select" required>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}">
                                        {{ $employee->name }} ({{ $employee->email }})
                                    </option>
                                @endforeach
                            </select>
                            <label for="employeeEmail">Select Employee</label>
                        </div>

                        <div class="form-floating">
                            <select name="status" id="status" class="form-select" required>
                                <option value="Present">Present</option>
                                <option value="Absent">Absent</option>
                            </select>
                            <label for="status">Attendance Status</label>
                        </div>
                    </div>

                    <div class="action-row">
                        <button class="btn primary-btn" type="submit">
                            <i class="bi bi-floppy"></i>
                            Save Attendance
                        </button>
                        <a href="{{ route('attendance.record') }}" class="btn secondary-btn">
                            <i class="bi bi-bar-chart-line"></i>
                            View Monthly Record
                        </a>
                    </div>
                </form>
            </section>

            
        </div>

        <section class="panel-card table-panel">
            <div class="table-toolbar">
                <div>
                    <p class="panel-kicker">Attendance Log</p>
                    <h4 class="mb-0">Attendance Log</h4>
                </div>
                <div class="toolbar-actions">
                    <span class="toolbar-badge">{{ $attendances->total() }} records</span>
                    <form method="GET" action="{{ route('attendance-admin') }}" class="d-flex gap-2 align-items-center">
                        <input type="date" name="date" class="form-control" value="{{ $selectedDate }}">
                        <button type="submit" class="btn secondary-btn">Load</button>
                    </form>
                    <div class="table-search">
                        <i class="bi bi-search"></i>
                        <input type="text" id="attendanceSearch" placeholder="Search employee or email">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table attendance-table align-middle mb-0" id="attendanceTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $a)
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <span class="avatar-pill">{{ strtoupper(substr($a->user->name ?? 'U', 0, 1)) }}</span>
                                        <span>{{ $a->user->name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td>{{ $a->user->email ?? '-' }}</td>
                                <td><span class="status-badge status-{{ strtolower($a->status) }}">{{ $a->status }}</span></td>
                                <td>{{ \Carbon\Carbon::parse($a->date)->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="empty-state">
                                        <i class="bi bi-calendar-x"></i>
                                        <h5>No attendance found</h5>
                                        <p>Mark the first attendance record from the form above.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($attendances->hasPages())
                <div class="pagination-shell">
                    {{ $attendances->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </section>
    </section>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/attendance-admin.js') }}"></script>
@endsection
