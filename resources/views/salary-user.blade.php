@extends('layouts.app')

@section('title', 'Salary Management - User')
@section('page_title', 'My Salary')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/salary-user.css') }}">
@endsection

@section('content')
    <section class="salary-shell">
        <div class="salary-hero">
            <div>
                <p class="eyebrow">My Payroll</p>
                <h3>Review your salary history and open each salary slip from the table.</h3>
                <p>Each salary record includes status, monthly totals, and a direct View Slip action that opens your detailed salary slip page.</p>
            </div>
            <div class="hero-chip">
                <span class="hero-chip-label">My Records</span>
                <strong>{{ $salaryStats['total'] ?? $salaries->total() }}</strong>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success salary-alert">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger salary-alert">{{ session('error') }}</div>
        @endif

        <div class="salary-stats-grid">
            <article class="metric-card">
                <div class="metric-icon"><i class="bi bi-receipt"></i></div>
                <div>
                    <span class="metric-label">Salary Records</span>
                    <h4>{{ $salaryStats['total'] ?? $salaries->total() }}</h4>
                </div>
            </article>
            <article class="metric-card">
                <div class="metric-icon"><i class="bi bi-check-circle"></i></div>
                <div>
                    <span class="metric-label">Paid</span>
                    <h4>{{ $salaryStats['paid'] ?? 0 }}</h4>
                </div>
            </article>
            <article class="metric-card">
                <div class="metric-icon"><i class="bi bi-hourglass-split"></i></div>
                <div>
                    <span class="metric-label">Pending</span>
                    <h4>{{ $salaryStats['pending'] ?? 0 }}</h4>
                </div>
            </article>
        </div>

        <section class="panel-card table-panel">
            <div class="table-toolbar">
                <div>
                    <p class="panel-kicker">Salary History</p>
                    <h4 class="mb-0">My Salary Records</h4>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table salary-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Present Days</th>
                            <th>Daily Rate</th>
                            <th>Basic</th>
                            <th>Bonus</th>
                            <th>Deduction</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Slip</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($salaries as $s)
                            <tr>
                                @php
                                    try {
                                        $salaryMonthLabel = \Carbon\Carbon::createFromFormat('Y-m', $s->month)->format('F Y');
                                    } catch (\Throwable $e) {
                                        $salaryMonthLabel = \Illuminate\Support\Str::title($s->month);
                                    }
                                @endphp
                                <td>{{ $salaryMonthLabel }}</td>
                                <td>{{ $s->present_days ?? 0 }}</td>
                                <td>{{ number_format($s->daily_rate ?? 0, 2) }}</td>
                                <td>{{ number_format($s->basic_salary, 2) }}</td>
                                <td>{{ number_format($s->bonus, 2) }}</td>
                                <td>{{ number_format($s->deduction, 2) }}</td>
                                <td>{{ number_format($s->net_salary, 2) }}</td>
                                <td>
                                    <span class="status-badge status-{{ strtolower($s->status) }}">{{ $s->status }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('salary-slip', ['id' => $s->id]) }}" class="btn slip-btn">
                                        View Slip
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">
                                    <div class="empty-state">
                                        <i class="bi bi-wallet2"></i>
                                        <h5>No salary records found</h5>
                                        <p>Your salary history will appear here once an admin or HR adds a record.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($salaries->hasPages())
                <div class="pagination-shell">
                    {{ $salaries->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </section>
    </section>
@endsection
