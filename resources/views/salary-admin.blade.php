@extends('layouts.app')

@section('title', 'Salary Management - Admin')
@section('page_title', 'Salary Management')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/salary-admin.css') }}">
@endsection

@section('content')
    <section class="salary-shell">
        <div class="salary-hero">
            <div>
                <p class="eyebrow">Payroll Workspace</p>
                <h3>Manage salary records, payment status, and employee slips in one place.</h3>
                <p>Create salary entries for HR, Manager, and Employee accounts, then open the slip page instantly from the same EMS workflow.</p>
            </div>
            <div class="hero-chip">
                <span class="hero-chip-label">Salary Records</span>
                <strong>{{ $salaryStats['total'] ?? $salaries->total() }}</strong>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success salary-alert">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger salary-alert">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger salary-alert">{{ $errors->first() }}</div>
        @endif

        <div class="salary-stats-grid">
            <article class="metric-card">
                <div class="metric-icon"><i class="bi bi-wallet2"></i></div>
                <div>
                    <span class="metric-label">Total Records</span>
                    <h4>{{ $salaryStats['total'] ?? $salaries->total() }}</h4>
                </div>
            </article>
            <article class="metric-card">
                <div class="metric-icon"><i class="bi bi-check-circle"></i></div>
                <div>
                    <span class="metric-label">Paid Salaries</span>
                    <h4>{{ $salaryStats['paid'] ?? 0 }}</h4>
                </div>
            </article>
            <article class="metric-card">
                <div class="metric-icon"><i class="bi bi-hourglass-split"></i></div>
                <div>
                    <span class="metric-label">Pending Salaries</span>
                    <h4>{{ $salaryStats['pending'] ?? 0 }}</h4>
                </div>
            </article>
        </div>

        <section class="panel-card form-panel">
            <div id="salaryPreviewConfig" data-preview-url="{{ route('salary.preview') }}"></div>
            <div class="panel-head">
                <div>
                    <p class="panel-kicker">Add Payroll</p>
                    <h4>Create a salary record</h4>
                </div>
                <span class="panel-note">Monthly salary is now calculated from Present attendance days x the employee's fixed daily salary.</span>
            </div>

            @if (in_array(strtolower(trim(auth()->user()->role ?? '')), ['admin', 'hr', 'manager']))
                <form method="POST" action="{{ route('salary.store') }}" class="salary-form">
                    @csrf

                    <div class="grid-2">
                        <div class="form-floating">
                            <select name="user_id" class="form-select" id="salaryUser" required>
                                <option value="">Select User</option>
                                @foreach ($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                                @endforeach
                            </select>
                            <label for="salaryUser">Select User</label>
                        </div>

                        <div class="form-floating">
                            <input type="month" name="month" class="form-control" id="salaryMonth" placeholder="Month" required>
                            <label for="salaryMonth">Month</label>
                        </div>

                        <div class="form-floating">
                            <input type="number" name="daily_rate" class="form-control" id="dailyRate" placeholder="Daily Salary" min="0" step="0.01">
                            <label for="dailyRate">Daily Salary</label>
                        </div>

                        <div class="form-floating">
                            <input type="number" class="form-control" id="presentDays" placeholder="Present Days" readonly>
                            <label for="presentDays">Present Days</label>
                        </div>

                        <div class="form-floating">
                            <input type="number" class="form-control" id="attendanceSalary" placeholder="Attendance Salary" readonly>
                            <label for="attendanceSalary">Attendance Salary</label>
                        </div>

                        <div class="form-floating">
                            <input type="number" name="bonus" class="form-control" id="bonusSalary" placeholder="Bonus">
                            <label for="bonusSalary">Bonus</label>
                        </div>

                        <div class="form-floating">
                            <input type="number" name="deduction" class="form-control" id="deductionSalary" placeholder="Deduction">
                            <label for="deductionSalary">Deduction</label>
                        </div>

                        <div class="form-floating">
                            <select name="status" class="form-select" id="salaryStatus">
                                <option value="Pending">Pending</option>
                                <option value="Paid">Paid</option>
                            </select>
                            <label for="salaryStatus">Status</label>
                        </div>
                    </div>

                    <div class="alert alert-info salary-alert mb-0" id="salaryPreviewInfo">
                        Select user and month to calculate salary from attendance.
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn primary-btn">
                            <i class="bi bi-plus-circle"></i>
                            Add Salary
                        </button>
                    </div>
                </form>
            @else
                <div class="alert alert-danger mb-0">Only Admin, HR, or Manager can add salary.</div>
            @endif
        </section>

        <section class="panel-card table-panel">
            <div class="table-toolbar">
                <div>
                    <p class="panel-kicker">Payroll Records</p>
                    <h4 class="mb-0">Salary List</h4>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table salary-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Email</th>
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
                                <td>{{ $s->user->email ?? '-' }}</td>
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
                                <td colspan="10">
                                    <div class="empty-state">
                                        <i class="bi bi-receipt"></i>
                                        <h5>No salary records found</h5>
                                        <p>Create a salary entry from the form above to populate the payroll table.</p>
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

@section('scripts')
    <script src="{{ asset('assets/js/salary-admin.js') }}"></script>
@endsection
