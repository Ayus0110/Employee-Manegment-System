@extends('layouts.app')

@section('title', 'Employee Details')
@section('page_title', 'Employee Details')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/employee-details.css') }}">
@endsection

@section('content')
    <section class="employee-shell">
        <div class="employee-hero">
            <div>
                <p class="eyebrow">Shift Management</p>
                <h3>{{ strtolower(auth()->user()->role ?? '') === 'admin' ? 'Assign and manage employee shifts from one dashboard.' : 'View your assigned shift and employee schedule details.' }}</h3>
                <p>{{ strtolower(auth()->user()->role ?? '') === 'admin' ? 'Schedule shifts for HR, Manager, and Employee accounts, then edit or delete them anytime from the same EMS page.' : 'Your assigned shift, department, and employee record are shown below in the same EMS workspace style.' }}</p>
            </div>
            <div class="hero-chip">
                <span class="hero-chip-label">Scheduled Records</span>
                <strong>{{ $stats['scheduled_shifts'] }}</strong>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success employee-alert">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger employee-alert">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger employee-alert">{{ $errors->first() }}</div>
        @endif

        <div class="employee-stats-grid">
            <article class="metric-card">
                <div class="metric-icon"><i class="bi bi-people"></i></div>
                <div>
                    <span class="metric-label">Records</span>
                    <h3>{{ $stats['total_records'] }}</h3>
                </div>
            </article>
            <article class="metric-card">
                <div class="metric-icon"><i class="bi bi-building"></i></div>
                <div>
                    <span class="metric-label">Departments</span>
                    <h3>{{ $stats['assigned_departments'] }}</h3>
                </div>
            </article>
            <article class="metric-card">
                <div class="metric-icon"><i class="bi bi-clock-history"></i></div>
                <div>
                    <span class="metric-label">Shift Plans</span>
                    <h3>{{ $stats['scheduled_shifts'] }}</h3>
                </div>
            </article>
        </div>

        @if (in_array(strtolower(auth()->user()->role ?? ''), ['admin', 'hr']))
            <section class="panel-card form-panel">
                <div class="panel-head">
                    <div>
                        <p class="panel-kicker">Schedule Form</p>
                        <h4>Assign or update a shift</h4>
                    </div>
                    <span class="panel-note">Choose a user, map the department, and define the shift pattern.</span>
                </div>

                <form action="{{ route('employee-details.store') }}" method="POST" class="employee-form" id="employeeDetailsForm">
                    @csrf

                    <div class="grid-2">
                        <div class="form-floating">
                            <select name="user_id" id="employeeUser" class="form-select" required>
                                <option value="">Select User</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} - {{ $user->role }}</option>
                                @endforeach
                            </select>
                            <label for="employeeUser">User</label>
                        </div>

                        <div class="form-floating">
                            <input type="text" name="employee_id" id="employeeCode" class="form-control" placeholder="Employee ID" required>
                            <label for="employeeCode">Employee ID</label>
                        </div>

                        <div class="form-floating">
                            <select name="department_id" id="employeeDepartment" class="form-select">
                                <option value="">Select Department</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                            <label for="employeeDepartment">Department</label>
                        </div>

                       

                        <div class="form-floating">
                            <select name="schedule_type" id="scheduleType" class="form-select" required>
                                <option value="Morning">Morning</option>
                                <option value="General" selected>General</option>
                                <option value="Evening">Evening</option>
                                <option value="Night">Night</option>
                                <option value="Custom">Custom</option>
                            </select>
                            <label for="scheduleType">Schedule Type</label>
                        </div>

                       

                        <div class="form-floating custom-shift-field">
                            <input type="time" name="shift_start" id="shiftStart" class="form-control" placeholder="Shift Start">
                            <label for="shiftStart">Shift Start</label>
                        </div>

                        <div class="form-floating custom-shift-field">
                            <input type="time" name="shift_end" id="shiftEnd" class="form-control" placeholder="Shift End">
                            <label for="shiftEnd">Shift End</label>
                        </div>

                        
                        
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn primary-btn">
                            <i class="bi bi-floppy"></i>
                            Save Shift
                        </button>
                    </div>
                </form>
            </section>
        @endif

        <section class="panel-card table-panel">
            <div class="table-toolbar">
                <div>
                    <p class="panel-kicker">{{ strtolower(auth()->user()->role ?? '') === 'admin' ? 'Employee Records' : 'My Shift' }}</p>
                    <h4 class="mb-0">{{ strtolower(auth()->user()->role ?? '') === 'admin' ? 'Employee Shift Records' : 'My Shift Details' }}</h4>
                </div>
                @if (in_array(strtolower(auth()->user()->role ?? ''), ['admin', 'hr']))
                    <div class="table-search">
                        <i class="bi bi-search"></i>
                        <input type="text" id="searchEmployee" class="form-control" placeholder="Search employee, email, role, or shift">
                    </div>
                @endif
            </div>

            <div class="table-responsive">
                <table class="table employee-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Department</th>
                            <th>Shift Type</th>
                            <th>Shift Time</th>
                            @if (in_array(strtolower(auth()->user()->role ?? ''), ['admin', 'hr']))
                                <th>Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody id="employeeTableBody">
                        @forelse ($employees as $emp)
                            <tr>
                                <td>{{ $employees->firstItem() + $loop->index }}</td>
                                <td>{{ $emp->user->name ?? '-' }}</td>
                                <td>{{ $emp->user->email ?? '-' }}</td>
                                <td>{{ $emp->user->phone ?? '-' }}</td>
                                <td>{{ $emp->department->name ?? 'Not assigned' }}</td>
                                <td>
                                    <span class="shift-badge">{{ $emp->schedule_type ?: 'Not assigned' }}</span>
                                </td>
                                <td>
                                    @if ($emp->shift_start && $emp->shift_end)
                                        {{ \Carbon\Carbon::createFromFormat('H:i:s', $emp->shift_start)->format('h:i A') }}
                                        -
                                        {{ \Carbon\Carbon::createFromFormat('H:i:s', $emp->shift_end)->format('h:i A') }}
                                    @else
                                        Not set
                                    @endif
                                </td>
                                @if (in_array(strtolower(auth()->user()->role ?? ''), ['admin', 'hr']))
                                    <td>
                                        <div class="table-actions">
                                            <button
                                                type="button"
                                                class="btn action-btn edit-btn"
                                                data-user-id="{{ $emp->user_id }}"
                                                data-employee-id="{{ $emp->employee_id }}"
                                                data-department-id="{{ $emp->department_id }}"
                                                data-designation="{{ $emp->designation }}"
                                                data-dob="{{ $emp->dob }}"
                                                data-address="{{ $emp->address }}"
                                                data-basic-salary="{{ $emp->basic_salary }}"
                                                data-schedule-type="{{ $emp->schedule_type }}"
                                                data-shift-start="{{ $emp->shift_start ? \Carbon\Carbon::createFromFormat('H:i:s', $emp->shift_start)->format('H:i') : '' }}"
                                                data-shift-end="{{ $emp->shift_end ? \Carbon\Carbon::createFromFormat('H:i:s', $emp->shift_end)->format('H:i') : '' }}"
                                            >
                                                <i class="bi bi-pencil-square"></i>
                                                Edit
                                            </button>

                                            <form method="POST" action="{{ route('employee-details.destroy', ['id' => $emp->id]) }}" onsubmit="return confirm('Delete this shift record?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn action-btn delete-btn">
                                                    <i class="bi bi-trash"></i>
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ in_array(strtolower(auth()->user()->role ?? ''), ['admin', 'hr']) ? 8 : 7 }}">
                                    <div class="empty-state">
                                        <i class="bi bi-clock-history"></i>
                                        <h5>No shift records yet</h5>
                                        <p>{{ in_array(strtolower(auth()->user()->role ?? ''), ['admin', 'hr']) ? 'Assign the first shift from the form above to populate this table.' : 'Your shift has not been assigned yet. Please contact admin.' }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($employees->hasPages())
                <div class="pagination-shell">
                    {{ $employees->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </section>
    </section>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/employee-details.js') }}"></script>
@endsection
