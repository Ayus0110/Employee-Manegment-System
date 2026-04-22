@extends('layouts.app')

@section('title', 'Departments')
@section('page_title', 'Departments')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/departments.css') }}">
@endsection

@section('content')
    <section class="department-shell">
        <div class="department-hero">
            <div>
                <p class="eyebrow">Workspace Overview</p>
                <h3>Build a clearer structure for every team.</h3>
                <p class="hero-copy">Track departments, assign heads, and quickly spot how your workforce is distributed across the organization.</p>
            </div>
            <div class="hero-chip">
                <span class="hero-chip-label">Largest Department</span>
                <strong>{{ $stats['largest_department'] }}</strong>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success department-alert">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger department-alert">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="department-stats-grid">
            <article class="metric-card">
                <div class="metric-icon"><i class="bi bi-building"></i></div>
                <div>
                    <span class="metric-label">Total Departments</span>
                    <h3>{{ $stats['total_departments'] }}</h3>
                </div>
            </article>
            <article class="metric-card">
                <div class="metric-icon"><i class="bi bi-people"></i></div>
                <div>
                    <span class="metric-label">Mapped Employees</span>
                    <h3>{{ $stats['total_employees'] }}</h3>
                </div>
            </article>
            <article class="metric-card">
                <div class="metric-icon"><i class="bi bi-person-badge"></i></div>
                <div>
                    <span class="metric-label">Leadership Coverage</span>
                    <h3>{{ $stats['leadership_coverage'] }}</h3>
                </div>
            </article>
        </div>

        <div class="department-grid">
            <section class="panel-card form-panel">
                <div class="panel-head">
                    <div>
                        <p class="panel-kicker">Create Department</p>
                        <h4>Add a new department</h4>
                    </div>
                    <span class="panel-note">Name and department head are enough to get started.</span>
                </div>

                <form action="{{ route('departments.store') }}" method="POST" class="department-form">
                    @csrf
                    <div class="form-floating">
                        <input
                            type="text"
                            name="name"
                            id="departmentName"
                            class="form-control"
                            placeholder="Department Name"
                            value="{{ old('name') }}"
                            required
                        >
                        <label for="departmentName">Department Name</label>
                    </div>

                    <div class="form-floating">
                        <input
                            type="text"
                            name="head"
                            id="departmentHead"
                            class="form-control"
                            placeholder="Department Head"
                            value="{{ old('head') }}"
                        >
                        <label for="departmentHead">Department Head</label>
                    </div>

                    <button class="btn department-submit-btn" type="submit">
                        <i class="bi bi-plus-circle"></i>
                        Add Department
                    </button>
                </form>
            </section>
        </div>

        <section class="panel-card table-panel">
            <div class="table-toolbar">
                <div>
                    
                    
                    <h4 class="mb-0">Department List</h4>
                </div>
                <div class="table-search">
                    <i class="bi bi-search"></i>
                    <input type="text" id="searchDepartment" class="form-control" placeholder="Search department or head">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table department-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Department</th>
                            <th>Head</th>
                            <th>Employees</th>
                            <th>Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="departmentTableBody">
                        @forelse ($departments as $index => $dep)
                            <tr>
                                <td>{{ $departments->firstItem() + $index }}</td>
                                <td>
                                    <div class="department-name-cell">
                                        <span class="department-avatar">{{ strtoupper(substr($dep->name, 0, 1)) }}</span>
                                        <div>
                                            <strong>{{ $dep->name }}</strong>
                                            <small>Core business unit</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $dep->head ?: 'Not assigned' }}</td>
                                <td>
                                    <span class="employee-badge">{{ $dep->employees_count }}</span>
                                </td>
                                <td>{{ optional($dep->created_at)->format('d M Y') }}</td>
                                <td>
                                    <div class="table-actions">
                                        <button
                                            type="button"
                                            class="btn action-btn edit-btn"
                                            data-id="{{ $dep->id }}"
                                            data-name="{{ $dep->name }}"
                                            data-head="{{ $dep->head }}"
                                        >
                                            <i class="bi bi-pencil-square"></i>
                                            Edit
                                        </button>

                                        <form action="{{ route('departments', ['id' => $dep->id]) }}" method="POST" onsubmit="return confirm('Delete this department?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn action-btn delete-btn">
                                                <i class="bi bi-trash"></i>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <i class="bi bi-building-add"></i>
                                        <h5>No departments yet</h5>
                                        <p>Create your first department to start organizing the workspace.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($departments->hasPages())
                <div class="pagination-shell">
                    {{ $departments->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </section>
    </section>

    <div class="department-modal" id="editDepartmentModal">
        <div class="department-modal-card">
            <div class="department-modal-head">
                <div>
                    <p class="panel-kicker">Update Department</p>
                    <h4>Edit department</h4>
                </div>
                <button type="button" class="modal-close-btn" id="closeDepartmentModal">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <form method="POST" id="editDepartmentForm" class="department-form">
                @csrf
                @method('PUT')

                <div class="form-floating">
                    <input type="text" name="name" id="editDepartmentName" class="form-control" placeholder="Department Name" required>
                    <label for="editDepartmentName">Department Name</label>
                </div>

                <div class="form-floating">
                    <input type="text" name="head" id="editDepartmentHead" class="form-control" placeholder="Department Head">
                    <label for="editDepartmentHead">Department Head</label>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn secondary-btn" id="cancelDepartmentModal">Cancel</button>
                    <button type="submit" class="btn department-submit-btn">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/departments.js') }}"></script>
@endsection


