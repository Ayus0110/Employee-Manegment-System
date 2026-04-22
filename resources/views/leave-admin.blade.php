@extends('layouts.app')

@section('title', 'Leave Management')
@section('page_title', 'Leave Requests')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/leave-management.css') }}">
@endsection

@section('content')
    @php
        $pendingCount = $leaveStats['pending'] ?? 0;
        $approvedCount = $leaveStats['approved'] ?? 0;
        $rejectedCount = $leaveStats['rejected'] ?? 0;
    @endphp

    <section class="leave-shell">
        <div class="leave-hero">
            <div class="hero-copy">
                <p class="eyebrow">Admin Leave Desk</p>
                <h3>Review employee leave requests from one clean workspace.</h3>
                <p>Approve or reject requests quickly, track pending items, and keep the leave approval flow clear for the whole team.</p>
            </div>
            <div class="hero-side">
                <div class="hero-chip">
                    <span class="hero-chip-label">Total Requests</span>
                    <strong>{{ $leaveStats['total'] ?? $leaves->total() }}</strong>
                </div>
                <div class="hero-chip muted-chip">
                    <span class="hero-chip-label">Pending Review</span>
                    <strong>{{ $pendingCount }}</strong>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success leave-alert">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger leave-alert">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger leave-alert">{{ $errors->first() }}</div>
        @endif

        <div class="stats-grid">
            <article class="metric-card metric-pending">
                <div class="metric-icon"><i class="bi bi-hourglass-split"></i></div>
                <div>
                    <span class="metric-label">Pending</span>
                    <h4>{{ $pendingCount }}</h4>
                    <p>Requests waiting for action</p>
                </div>
            </article>
            <article class="metric-card metric-approved">
                <div class="metric-icon"><i class="bi bi-check-circle-fill"></i></div>
                <div>
                    <span class="metric-label">Approved</span>
                    <h4>{{ $approvedCount }}</h4>
                    <p>Approved leave requests</p>
                </div>
            </article>
            <article class="metric-card metric-rejected">
                <div class="metric-icon"><i class="bi bi-x-circle-fill"></i></div>
                <div>
                    <span class="metric-label">Rejected</span>
                    <h4>{{ $rejectedCount }}</h4>
                    <p>Rejected leave requests</p>
                </div>
            </article>
        </div>

        <section class="panel-card table-panel">
            <div class="table-toolbar">
                <div>
                    <p class="panel-kicker">Admin Requests</p>
                    <h4 class="mb-0">Leave Request List</h4>
                </div>
                <div class="toolbar-actions">
                    <span class="toolbar-badge">{{ $leaveStats['total'] ?? $leaves->total() }} requests</span>
                    <div class="table-search">
                        <i class="bi bi-search"></i>
                        <input type="text" id="leaveAdminSearch" placeholder="Search email, reason, or status">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table leave-table align-middle mb-0" id="leaveAdminTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>Date</th>
                            <th>Days</th>
                            <th>Type</th>
                            <th>Balance</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($leaves as $index => $leave)
                            <tr>
                                <td>{{ $leaves->firstItem() + $index }}</td>
                                <td>
                                    <div class="user-cell">
                                        <span class="avatar-pill">{{ strtoupper(substr($leave->user->name ?? 'U', 0, 1)) }}</span>
                                        <div>
                                            <strong>{{ $leave->user->name ?? '-' }}</strong>
                                            <small>{{ $leave->user->email ?? '-' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($leave->from_date)->format('d M Y') }}</td>
                                <td>{{ $leave->days }}</td>
                                <td>{{ $leave->type }}</td>
                                <td>
                                    @php
                                        $balanceColumn = match($leave->type) {
                                            'Casual Leave' => 'casual_leave_balance',
                                            'Sick Leave' => 'sick_leave_balance',
                                            'Paid Leave' => 'paid_leave_balance',
                                            default => null,
                                        };
                                    @endphp
                                    {{ $balanceColumn ? ($leave->user->{$balanceColumn} ?? 0) . ' day(s)' : '-' }}
                                </td>
                                <td class="reason-cell">{{ $leave->reason }}</td>
                                <td>
                                    <span class="status-badge status-{{ strtolower($leave->status) }}">{{ $leave->status }}</span>
                                </td>
                                <td>
                                    @if (strtolower(trim($leave->status)) == 'pending')
                                        <div class="action-group">
                                            <form method="POST" action="{{ route('leave.update-status', $leave->id) }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="Approved">
                                                <button type="submit" class="btn action-btn approve-btn">Approve</button>
                                            </form>
                                            <form method="POST" action="{{ route('leave.update-status', $leave->id) }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="Rejected">
                                                <button type="submit" class="btn action-btn reject-btn">Reject</button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="muted-note">Already {{ $leave->status }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">
                                    <div class="empty-state">
                                        <i class="bi bi-inbox"></i>
                                        <h5>No leave requests yet</h5>
                                        <p>New employee leave requests will appear here for review.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($leaves->hasPages())
                <div class="pagination-shell">
                    {{ $leaves->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </section>
    </section>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/leave-admin.js') }}"></script>
@endsection
