<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary Slip</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/salary-slip.css') }}">
</head>
<body>
    @php
        try {
            $salaryMonthLabel = \Carbon\Carbon::createFromFormat('Y-m', $salary->month)->format('F Y');
        } catch (\Throwable $e) {
            $salaryMonthLabel = \Illuminate\Support\Str::title($salary->month);
        }
    @endphp
    <div class="slip-shell">
        <div class="slip-card">
            <div class="slip-head">
                <div>
                    <span class="eyebrow">EMS Payroll Slip</span>
                    <h1>Salary Slip</h1>
                    <p class="slip-subtitle">This document summarizes the employee salary breakdown, payment status, and monthly payroll details.</p>
                </div>

                <div class="slip-actions no-print">
                    <a href="{{ route('salary-slip.download', ['id' => $salary->id]) }}" class="slip-btn slip-btn-primary">Download PDF</a>
                    <a href="{{ url()->previous() }}" class="slip-btn slip-btn-secondary">Back</a>
                </div>
            </div>

            <div class="slip-summary-grid">
                <div class="summary-card">
                    <span>Employee</span>
                    <strong>{{ $salary->user->name ?? '-' }}</strong>
                </div>
                <div class="summary-card">
                    <span>Month</span>
                    <strong>{{ $salaryMonthLabel }}</strong>
                </div>
                <div class="summary-card">
                    <span>Status</span>
                    <strong class="status-pill status-{{ strtolower($salary->status) }}">{{ $salary->status }}</strong>
                </div>
                <div class="summary-card">
                    <span>Date</span>
                    <strong>{{ optional($salary->created_at)->format('d M Y') ?? now()->format('d M Y') }}</strong>
                </div>
            </div>

            <div class="slip-grid">
                <div class="info-card">
                    <h2 class="section-title">Employee Details</h2>
                    <div class="info-list">
                        <div class="info-item">
                            <span>Employee Name</span>
                            <strong>{{ $salary->user->name ?? '-' }}</strong>
                        </div>
                        <div class="info-item">
                            <span>Email</span>
                            <strong>{{ $salary->user->email ?? '-' }}</strong>
                        </div>
                        <div class="info-item">
                            <span>Role</span>
                            <strong>{{ $salary->user->role ?? '-' }}</strong>
                        </div>
                        <div class="info-item">
                            <span>Payment Month</span>
                            <strong>{{ $salaryMonthLabel }}</strong>
                        </div>
                        <div class="info-item">
                            <span>Present Days</span>
                            <strong>{{ $salary->present_days ?? 0 }}</strong>
                        </div>
                        <div class="info-item">
                            <span>Daily Salary</span>
                            <strong>{{ number_format($salary->daily_rate ?? 0, 2) }}</strong>
                        </div>
                    </div>
                </div>

                <div class="breakdown-card">
                    <h2 class="section-title">Salary Breakdown</h2>
                    <table class="salary-breakdown">
                        <tbody>
                            <tr>
                                <th>Attendance Salary</th>
                                <td>{{ number_format($salary->basic_salary, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Bonus</th>
                                <td>{{ number_format($salary->bonus, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Deduction</th>
                                <td>{{ number_format($salary->deduction, 2) }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>Net Salary</td>
                                <td>{{ number_format($salary->net_salary, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="authorized-box">
                <strong>Authorized By:</strong> EMS Management
            </div>
        </div>
    </div>
</body>
</html>
