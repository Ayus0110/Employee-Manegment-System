@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Analytics Dashboard')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
    
@endsection

@section('content')
<div class="dashboard-shell">
    <section class="dashboard-hero">
        <div>
            <p class="eyebrow">Operations Overview</p>
            <h3>Track people, departments, shifts, and payroll from one dashboard.</h3>
            <p>See the latest user activity, employee records, and workforce metrics inside the same EMS workspace style.</p>
        </div>
        <div class="hero-chip">
            <span class="hero-chip-label">Active Members</span>
            <strong>{{ $userCount }}</strong>
        </div>
    </section>

    <section class="stats-grid">
        <article class="metric-card">
            <div class="metric-icon"><i class="bi bi-clock-history"></i></div>
            <div>
                <span class="metric-label">Scheduled Shifts</span>
                <h4>{{ $scheduledShiftCount }}</h4>
            </div>
        </article>

        <article class="metric-card">
            <div class="metric-icon"><i class="bi bi-building"></i></div>
            <div>
                <span class="metric-label">Departments</span>
                <h4>{{ $departmentCount }}</h4>
            </div>
        </article>

        <article class="metric-card">
            <div class="metric-icon"><i class="bi bi-person-badge"></i></div>
            <div>
                <span class="metric-label">Employees</span>
                <h4>{{ $employeeCount }}</h4>
            </div>
        </article>

        <article class="metric-card">
            <div class="metric-icon"><i class="bi bi-people"></i></div>
            <div>
                <span class="metric-label">Users & Members</span>
                <h4>{{ $userCount }}</h4>
            </div>
        </article>
    </section>

    <section class="highlight-grid">
        <article class="panel-card highlight-card">
            <span class="panel-kicker">Leave Requests</span>
            <h4>{{ $pendingLeaveCount }}</h4>
            <p>Pending approvals that still need admin or HR action.</p>
        </article>

        <article class="panel-card highlight-card">
            <span class="panel-kicker">Paid Salaries</span>
            <h4>{{ $paidSalaryCount }}</h4>
            <p>Salary records already marked as paid in payroll management.</p>
        </article>

        <article class="panel-card highlight-card">
            <span class="panel-kicker">Records Snapshot</span>
            <h4>{{ $userCount + $employeeCount }}</h4>
            <p>Total user and employee records currently tracked in EMS.</p>
        </article>
    </section>

    <div class="dashboard-grid">
        <section class="panel-card">
            <div class="panel-head">
                <div>
                    <p class="panel-kicker">Users & Members</p>
                    <h4>Latest user accounts</h4>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table dashboard-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Date Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div class="user-cell">
                                        <span class="user-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                        <div>
                                            <strong>{{ $user->name }}</strong>
                                            <small>{{ $user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="role-badge role-{{ strtolower($user->role) }}">{{ $user->role }}</span></td>
                                <td>{{ optional($user->created_at)->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="empty-state">
                                        <i class="bi bi-people"></i>
                                        <h5>No users found</h5>
                                        <p>Create users from Manage Users to populate this section.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="panel-card">
            <div class="panel-head">
                <div>
                    <p class="panel-kicker">Employee Data</p>
                    <h4>Latest employee records</h4>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table dashboard-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Designation</th>
                            <th>Appointed</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($employees as $employee)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $employee->user->name ?? '-' }}</td>
                                <td>{{ $employee->department->name ?? 'Not assigned' }}</td>
                                <td>{{ $employee->designation ?: '-' }}</td>
                                <td>{{ optional($employee->created_at)->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <i class="bi bi-person-vcard"></i>
                                        <h5>No employee data yet</h5>
                                        <p>Add employee records from Employee Details to populate this table.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
@endsection
