@extends('layouts.app')

@section('title', 'Apply Leave')
@section('page_title', 'Leave Application')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/leave-management.css') }}">
@endsection

@section('content')
    @php
        $pendingCount = $leaveStats['pending'] ?? 0;
        $approvedCount = $leaveStats['approved'] ?? 0;
        $rejectedCount = $leaveStats['rejected'] ?? 0;
        $isEditing = isset($editLeave) && $editLeave;
    @endphp

    <section class="leave-shell">
        <div class="leave-hero">
            <div class="hero-copy">
                <p class="eyebrow">My Leave Workspace</p>
                <h3>Apply, track, and manage your leave requests from one page.</h3>
                <p>Submit a new leave request, update pending requests, and check approval progress without leaving the EMS dashboard.</p>
            </div>
            <div class="hero-side">
                <div class="hero-chip">
                    <span class="hero-chip-label">My Requests</span>
                    <strong>{{ $leaveStats['total'] ?? $leaves->total() }}</strong>
                </div>
                <div class="hero-chip muted-chip">
                    <span class="hero-chip-label">Pending</span>
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
            <article class="metric-card metric-balance">
                <div class="metric-icon"><i class="bi bi-briefcase"></i></div>
                <div>
                    <span class="metric-label">Casual Leave</span>
                    <h4>{{ $leaveStats['balances']['Casual Leave'] ?? 0 }}</h4>
                    <p>Days available in your balance</p>
                </div>
            </article>
            <article class="metric-card metric-balance">
                <div class="metric-icon"><i class="bi bi-heart-pulse"></i></div>
                <div>
                    <span class="metric-label">Sick Leave</span>
                    <h4>{{ $leaveStats['balances']['Sick Leave'] ?? 0 }}</h4>
                    <p>Days available in your balance</p>
                </div>
            </article>
            <article class="metric-card metric-balance">
                <div class="metric-icon"><i class="bi bi-cash-coin"></i></div>
                <div>
                    <span class="metric-label">Paid Leave</span>
                    <h4>{{ $leaveStats['balances']['Paid Leave'] ?? 0 }}</h4>
                    <p>Days available in your balance</p>
                </div>
            </article>
            <article class="metric-card metric-pending">
                <div class="metric-icon"><i class="bi bi-hourglass-split"></i></div>
                <div>
                    <span class="metric-label">Pending</span>
                    <h4>{{ $pendingCount }}</h4>
                    <p>Requests waiting for review</p>
                </div>
            </article>
            <article class="metric-card metric-approved">
                <div class="metric-icon"><i class="bi bi-check-circle-fill"></i></div>
                <div>
                    <span class="metric-label">Approved</span>
                    <h4>{{ $approvedCount }}</h4>
                    <p>Approved leave days</p>
                </div>
            </article>
            <article class="metric-card metric-rejected">
                <div class="metric-icon"><i class="bi bi-x-circle-fill"></i></div>
                <div>
                    <span class="metric-label">Rejected</span>
                    <h4>{{ $rejectedCount }}</h4>
                    <p>Requests not approved</p>
                </div>
            </article>
        </div>

        <div class="leave-grid">
            @if (strtolower(trim(auth()->user()->role)) == 'employee')
                <section class="panel-card form-panel">
                    <div class="panel-head">
                        <div>
                            <p class="panel-kicker">Leave Form</p>
                            <h4>{{ $isEditing ? 'Update Leave Request' : 'Apply for Leave' }}</h4>
                        </div>
                        <span class="panel-note">{{ $isEditing ? 'You can only update a request while it is still pending.' : 'Choose the date, duration, type, and reason for your leave.' }}</span>
                    </div>

                    <form method="POST" action="{{ $isEditing ? route('leave.update', $editLeave->id) : route('leave.store') }}" class="leave-form">
                        @csrf
                        @if ($isEditing)
                            @method('PUT')
                        @endif

                        <div class="grid-2">
                            <div class="form-floating">
                                <input type="date" name="date" id="leaveDate" class="form-control" value="{{ $isEditing ? $editLeave->from_date : '' }}" required>
                                <label for="leaveDate">Leave Date</label>
                            </div>

                            <div class="form-floating">
                                <input type="number" name="days" id="leaveDays" class="form-control" min="1" value="{{ $isEditing ? $editLeave->days : '' }}" required>
                                <label for="leaveDays">Number of Days</label>
                            </div>
                        </div>

                        <div class="grid-2">
                            <div class="form-floating">
                                <select name="type" id="leaveType" class="form-select" required>
                                    <option value="Casual Leave" {{ $isEditing && $editLeave->type == 'Casual Leave' ? 'selected' : '' }}>Casual Leave</option>
                                    <option value="Sick Leave" {{ $isEditing && $editLeave->type == 'Sick Leave' ? 'selected' : '' }}>Sick Leave</option>
                                    <option value="Paid Leave" {{ $isEditing && $editLeave->type == 'Paid Leave' ? 'selected' : '' }}>Paid Leave</option>
                                </select>
                                <label for="leaveType">Leave Type</label>
                            </div>

                            <div class="form-floating textarea-wrap">
                                <textarea name="reason" id="leaveReason" class="form-control textarea-control" placeholder="Leave Reason" required>{{ $isEditing ? $editLeave->reason : '' }}</textarea>
                                <label for="leaveReason">Leave Reason</label>
                            </div>
                        </div>

                        <div class="action-row">
                            <button class="btn primary-btn" type="submit">{{ $isEditing ? 'Update Leave' : 'Apply Leave' }}</button>
                            @if ($isEditing)
                                <a href="{{ route('leave-user') }}" class="btn secondary-btn">Cancel</a>
                            @endif
                        </div>

                        <div class="balance-note">
                            Casual: <strong>{{ $leaveStats['balances']['Casual Leave'] ?? 0 }}</strong> day(s),
                            Sick: <strong>{{ $leaveStats['balances']['Sick Leave'] ?? 0 }}</strong> day(s),
                            Paid: <strong>{{ $leaveStats['balances']['Paid Leave'] ?? 0 }}</strong> day(s)
                        </div>
                    </form>
                </section>
            @endif

            <aside class="panel-card insight-panel">
                <div class="panel-head slim-head">
                    <div>
                        <p class="panel-kicker">Leave Tips</p>
                        <h4>Keep your request clear</h4>
                    </div>
                </div>

                <div class="insight-list">
                    <div class="insight-item">
                        <span class="insight-dot dot-blue"></span>
                        <div>
                            <strong>Apply early</strong>
                            <p>Submit planned leave in advance so approval is easier for your team and manager.</p>
                        </div>
                    </div>
                    <div class="insight-item">
                        <span class="insight-dot dot-green"></span>
                        <div>
                            <strong>Explain the reason</strong>
                            <p>Use a short but clear reason so the request can be reviewed quickly.</p>
                        </div>
                    </div>
                    <div class="insight-item">
                        <span class="insight-dot dot-red"></span>
                        <div>
                            <strong>Edit only pending requests</strong>
                            <p>Once approved or rejected, the request becomes read-only in your dashboard.</p>
                        </div>
                    </div>
                </div>
            </aside>
        </div>

        <section class="panel-card table-panel">
            <div class="table-toolbar">
                <div>
                    <p class="panel-kicker">My Leave Requests</p>
                    <h4 class="mb-0">Request History</h4>
                </div>
                <div class="toolbar-actions">
                    <span class="toolbar-badge">{{ $leaveStats['total'] ?? $leaves->total() }} requests</span>
                    <div class="table-search">
                        <i class="bi bi-search"></i>
                        <input type="text" id="leaveUserSearch" placeholder="Search type, reason, or status">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table leave-table align-middle mb-0" id="leaveUserTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Days</th>
                            <th>Type</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($leaves as $index => $leave)
                            <tr>
                                <td>{{ $leaves->firstItem() + $index }}</td>
                                <td>{{ \Carbon\Carbon::parse($leave->from_date)->format('d M Y') }}</td>
                                <td>{{ $leave->days }}</td>
                                <td>{{ $leave->type }}</td>
                                <td class="reason-cell">{{ $leave->reason }}</td>
                                <td>
                                    <span class="status-badge status-{{ strtolower($leave->status) }}">{{ $leave->status }}</span>
                                </td>
                                <td>
                                    @if (strtolower(trim($leave->status)) == 'pending' && strtolower(trim(auth()->user()->role)) == 'employee')
                                        <div class="action-group">
                                            <a href="{{ route('leave-user', ['edit_id' => $leave->id]) }}" class="btn action-btn edit-btn">Edit</a>
                                            <form method="POST" action="{{ route('leave.destroy', $leave->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn action-btn reject-btn" onclick="return confirm('Are you sure you want to delete this leave request?')">Delete</button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="muted-note">{{ strtolower(trim($leave->status)) == 'pending' ? 'No action' : 'Locked after review' }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="bi bi-journal-x"></i>
                                        <h5>No leave requests yet</h5>
                                        <p>Your leave requests will appear here after you submit the first one.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($leaves->hasPages())
                <div class="pagination-shell">
                    {{ $leaves->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </section>
    </section>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/leave-user.js') }}"></script>
@endsection
