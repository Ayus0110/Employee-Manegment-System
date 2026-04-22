@extends('layouts.app')

@section('title', 'Manage Users - EMS')
@section('page_title', 'Manage Users')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/manage-user.css') }}">
@endsection

@section('content')
<div class="manage-user-shell">
    <div id="manageUserConfig" data-update-base-url="{{ url('/manage-user') }}"></div>
    <section class="manage-user-hero">
        <div>
            <p class="eyebrow">User Administration</p>
            <h3>Manage access, roles, and account records from one place.</h3>
            <p>Keep your user list clean, assign the right roles, and import or export account data without leaving the EMS workspace.</p>
        </div>
        <div class="hero-chip">
            <span class="hero-chip-label">Total Users</span>
            <strong>{{ $userStats['total'] }}</strong>
        </div>
    </section>

    @if(session('success'))
        <div class="alert alert-success manage-alert">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger manage-alert">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger manage-alert">{{ $errors->first() }}</div>
    @endif

    <section class="manage-metrics">
        <article class="metric-card">
                <div class="metric-icon"><i class="bi bi-people"></i></div>
                <div>
                    <span class="metric-label">All Accounts</span>
                    <h4>{{ $userStats['total'] }}</h4>
                </div>
            </article>
            <article class="metric-card">
                <div class="metric-icon"><i class="bi bi-person-workspace"></i></div>
                <div>
                    <span class="metric-label">Employees</span>
                    <h4>{{ $userStats['employees'] }}</h4>
                </div>
            </article>
            <article class="metric-card">
                <div class="metric-icon"><i class="bi bi-shield-lock"></i></div>
                <div>
                    <span class="metric-label">Admin / HR / Manager</span>
                    <h4>{{ $userStats['leadership'] }}</h4>
                </div>
            </article>
    </section>

    <section class="panel-card table-panel">
        <div class="table-toolbar">
            <div>
                <p class="panel-kicker">Directory</p>
                <h4 class="mb-0">Manage Users</h4>
            </div>
            <div class="toolbar-actions">
                <div class="table-search">
                    <i class="bi bi-search"></i>
                    <input type="text" id="searchUsers" class="form-control" placeholder="Search name, email, phone, or role">
                </div>
                <a href="{{ route('manage-user.export') }}" class="btn action-top-btn export-btn">
                    <i class="bi bi-download"></i> Export
                </a>
                <button class="btn action-top-btn import-btn" data-bs-toggle="modal" data-bs-target="#importUserModal">
                    <i class="bi bi-upload"></i> Import
                </button>
                <button class="btn action-top-btn add-btn" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="bi bi-plus"></i> Add New
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table manage-user-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th width="220">Action</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody">
                    @forelse ($users as $index => $user)
                        <tr>
                            <td>{{ $users->firstItem() + $index }}</td>
                            <td>
                                <div class="user-name-cell">
                                    <span class="user-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                    <div>
                                        <strong>{{ $user->name }}</strong>
                                        <small>User account</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone }}</td>
                            <td>
                                <span class="role-badge role-{{ strtolower($user->role) }}">{{ $user->role }}</span>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <button
                                        type="button"
                                        class="btn action-btn edit-btn editUserBtn"
                                        data-id="{{ $user->id }}"
                                        data-name="{{ $user->name }}"
                                        data-email="{{ $user->email }}"
                                        data-phone="{{ $user->phone }}"
                                        data-role="{{ $user->role }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editUserModal">
                                        <i class="bi bi-pencil-square"></i>
                                        Edit
                                    </button>

                                    <form method="POST" action="{{ route('manage-user.delete', $user->id) }}"
                                        onsubmit="return confirm('Are you sure you want to delete this user?')">
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
                                    <i class="bi bi-people"></i>
                                    <h5>No users available</h5>
                                    <p>Create or import users to populate this table.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="pagination-shell">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </section>
</div>

<!-- Import User Modal -->
<div class="modal fade" id="importUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content custom-modal">
            <form method="POST" action="{{ route('manage-user.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-0">
                    <h5 class="modal-title">Import Users</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info modal-alert">
                        Upload an Excel or CSV file with headings:
                        <strong>Name, Email, Phone, Role, Password</strong>
                    </div>

                    <input type="file" name="file" class="form-control" accept=".xlsx,.csv,.txt" required>

                    <small class="text-muted d-block mt-3">
                        If Password is empty, the system will create a random password automatically.
                    </small>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn modal-submit-btn import-btn-solid">Import Users</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content custom-modal">
            <form method="POST" action="{{ route('manage-user.store') }}">
                @csrf
                <div class="modal-header border-0">
                    <h5 class="modal-title">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" name="name" class="form-control modal-control mb-3" placeholder="Full Name" required>
                    <input type="email" name="email" class="form-control modal-control mb-3" placeholder="Email" required>
                    <input type="text" name="phone" class="form-control modal-control mb-3" placeholder="Phone Number" required>

                    <select name="role" class="form-select modal-control mb-3" required>
                        <option value="">Select Role</option>
                        <option value="Admin">Admin</option>
                        <option value="HR">HR</option>
                        <option value="Manager">Manager</option>
                        <option value="Employee">Employee</option>
                    </select>

                    <div class="form-check">
                        <input type="checkbox" name="send_email" value="1" id="sendEmail" class="form-check-input">
                        <label for="sendEmail" class="form-check-label">Send Email to User</label>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn modal-submit-btn add-btn-solid">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content custom-modal">
            <form method="POST" id="editUserForm">
                @csrf
                @method('PUT')

                <div class="modal-header border-0">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="text" name="name" id="edit_name" class="form-control modal-control mb-3" placeholder="Full Name" required>
                    <input type="email" name="email" id="edit_email" class="form-control modal-control mb-3" placeholder="Email" readonly>
                    <input type="text" name="phone" id="edit_phone" class="form-control modal-control mb-3" placeholder="Phone Number" required>

                    <select name="role" id="edit_role" class="form-select modal-control" required>
                        <option value="Admin">Admin</option>
                        <option value="HR">HR</option>
                        <option value="Manager">Manager</option>
                        <option value="Employee">Employee</option>
                    </select>
                </div>

                <div class="modal-footer border-0">
                    <button type="submit" class="btn modal-submit-btn edit-btn-solid">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('assets/js/manage-user.js') }}"></script>
@endsection
