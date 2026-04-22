@extends('layouts.app')

@section('title', 'Attendance Management')
@section('page_title', 'My Attendance')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/attendance-user.css') }}">
@endsection

@section('content')
    @php
        $presentCount = $attendanceStats['present'] ?? 0;
        $absentCount = $attendanceStats['absent'] ?? 0;
        $latestAttendance = $attendances->first();
    @endphp

    <div class="attendance-user-shell">
        <section class="attendance-user-hero">
            <div class="hero-copy">
                <p class="eyebrow">Personal Attendance</p>
                <h3>Track your attendance history with a cleaner daily view.</h3>
                <p>See your latest mark, monthly movement, and full attendance history in one professional employee workspace.</p>
            </div>

            <div class="hero-side">
                <article class="hero-chip">
                    <span class="hero-chip-label">Latest Mark</span>
                    <strong>{{ $latestAttendance?->status ?? 'No record' }}</strong>
                    <small>{{ $latestAttendance ? \Carbon\Carbon::parse($latestAttendance->date)->format('d M Y') : 'Waiting for first entry' }}</small>
                </article>
                <a href="{{ route('attendance.record') }}" class="hero-action-link">
                    <i class="bi bi-calendar3"></i>
                    View Monthly Record
                </a>
            </div>
        </section>

        <section class="attendance-stats-grid">
            <article class="metric-card metric-total">
                <div class="metric-icon"><i class="bi bi-card-checklist"></i></div>
                <div>
                    <span class="metric-label">Total Records</span>
                    <h4>{{ $attendanceStats['total'] ?? $attendances->total() }}</h4>
                    <p>Attendance entries available in your timeline.</p>
                </div>
            </article>

            <article class="metric-card metric-present">
                <div class="metric-icon"><i class="bi bi-check-circle"></i></div>
                <div>
                    <span class="metric-label">Present Days</span>
                    <h4>{{ $presentCount }}</h4>
                    <p>Days marked as present so far.</p>
                </div>
            </article>

            <article class="metric-card metric-absent">
                <div class="metric-icon"><i class="bi bi-x-circle"></i></div>
                <div>
                    <span class="metric-label">Absent Days</span>
                    <h4>{{ $absentCount }}</h4>
                    <p>Days marked absent in your record.</p>
                </div>
            </article>
        </section>

        <section class="panel-card">
            <div class="panel-head">
                <div>
                    <p class="panel-kicker">Attendance History</p>
                    <h4>Recent attendance records</h4>
                </div>
                <span class="panel-note">Latest entries are shown first so you can quickly review your recent attendance status.</span>
            </div>

            <div class="table-responsive">
                <table class="table attendance-user-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Date</th>
                            <th>Day</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $attendance)
                            <tr>
                                <td>{{ $attendances->firstItem() + $loop->index }}</td>
                                <td>
                                    <strong>{{ \Carbon\Carbon::parse($attendance->date)->format('d M Y') }}</strong>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($attendance->date)->format('l') }}</td>
                                <td>
                                    <span class="status-badge status-{{ strtolower($attendance->status) }}">
                                        {{ $attendance->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="empty-state">
                                        <i class="bi bi-calendar-x"></i>
                                        <h5>No attendance found</h5>
                                        <p>Your attendance history will appear here once your records are marked.</p>
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
    </div>
@endsection

