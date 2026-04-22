@extends('layouts.app')

@section('title', 'Attendance Management')
@section('page_title', 'Attendance Record')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/attendance-record.css') }}">
@endsection

@section('content')
    @php
        $monthLabel = \Carbon\Carbon::create($year, $month, 1)->format('F Y');
    @endphp

    <section class="record-shell">
        <section class="panel-card record-panel">
            <div class="panel-head compact-panel-head">
                <div>
                    <p class="panel-kicker">Attendance Analytics</p>
                    <h4>Monthly Attendance Record</h4>
                    <p class="panel-subtitle">{{ $selectedUser->name ?? 'Selected Employee' }} · {{ $monthLabel }}</p>
                </div>
                <div class="head-chips">
                    <span class="head-chip">Present {{ $presentCount }}</span>
                    <span class="head-chip danger-chip">Absent {{ $absentCount }}</span>
                    <span class="head-chip neutral-chip">Unmarked {{ $noneCount }}</span>
                </div>
            </div>

            <form method="GET" action="{{ route('attendance.record') }}" class="filter-grid compact-filter-grid">
                @if(in_array(strtolower(trim(auth()->user()->role ?? '')), ['admin', 'hr', 'manager']))
                    <div class="form-floating employee-field">
                        <select name="user_id" class="form-select" id="recordUser">
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ $selectedUser->id == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name }} ({{ $employee->email }})
                                </option>
                            @endforeach
                        </select>
                        <label for="recordUser">Select Employee</label>
                    </div>
                @endif

                <div class="form-floating">
                    <select name="month" class="form-select" id="recordMonth">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                            </option>
                        @endfor
                    </select>
                    <label for="recordMonth">Month</label>
                </div>

                <div class="form-floating">
                    <select name="year" class="form-select" id="recordYear">
                        @for($y = now()->year - 2; $y <= now()->year + 2; $y++)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                    <label for="recordYear">Year</label>
                </div>

                <div class="search-action">
                    <button type="submit" class="btn primary-btn w-100">
                        <i class="bi bi-search"></i>
                        Search
                    </button>
                </div>
            </form>

            <div class="content-grid compact-content-grid">
                <div class="calendar-panel">
                    <div class="section-head compact-head">
                        <div>
                            <p class="panel-kicker">Day Wise Timeline</p>
                            <h5>Attendance Calendar</h5>
                        </div>
                        <div class="legend-row">
                            <span class="legend-item"><i class="legend-dot present-dot"></i>P</span>
                            <span class="legend-item"><i class="legend-dot absent-dot"></i>A</span>
                            <span class="legend-item"><i class="legend-dot wo-dot"></i>WO</span>
                            <span class="legend-item"><i class="legend-dot none-dot"></i>-</span>
                        </div>
                    </div>

                    <div class="calendar-wrap compact-calendar-wrap">
                        <div class="calendar-row">
                            @foreach($calendar as $day)
                                @php
                                    $statusClass = match($day['status']) {
                                        'Present' => 'status-present',
                                        'Absent' => 'status-absent',
                                        'WO' => 'status-wo',
                                        default => 'status-none'
                                    };

                                    $statusShort = match($day['status']) {
                                        'Present' => 'P',
                                        'Absent' => 'A',
                                        'WO' => 'WO',
                                        default => '-'
                                    };
                                @endphp

                                <div class="day-card {{ $statusClass }}">
                                    <div class="day-num">{{ str_pad($day['day'], 2, '0', STR_PAD_LEFT) }}</div>
                                    <div class="weekday">{{ $day['weekday'] }}</div>
                                    <div class="status-text">{{ $statusShort }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="chart-panel compact-chart-panel">
                    <div class="section-head compact-head">
                        <div>
                            <p class="panel-kicker">Monthly Breakdown</p>
                            <h5>Attendance Mix</h5>
                        </div>
                    </div>

                    <div class="mini-stats">
                        <div class="mini-stat success-stat">
                            <span>Present</span>
                            <strong>{{ $presentCount }}</strong>
                        </div>
                        <div class="mini-stat danger-stat">
                            <span>Absent</span>
                            <strong>{{ $absentCount }}</strong>
                        </div>
                        <div class="mini-stat neutral-stat">
                            <span>Unmarked</span>
                            <strong>{{ $noneCount }}</strong>
                        </div>
                    </div>

                    <div class="chart-card compact-chart-card">
                        <canvas id="attendanceChart" data-present="{{ $presentCount }}" data-absent="{{ $absentCount }}" data-none="{{ $noneCount }}"></canvas>
                    </div>
                </div>
            </div>
        </section>
    </section>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('assets/js/attendance-record.js') }}"></script>
@endsection
