@extends('layouts.app')

@section('title', 'Task Assignment')
@section('page_title', 'Task Assignment')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/tasks.css') }}">
@endsection

@section('content')
    @php
        $assignedCount = $taskStats['assigned_count'] ?? 0;
        $myCount = $taskStats['my_count'] ?? 0;
        $pendingMyCount = $taskStats['pending_my_count'] ?? 0;
        $completedMyCount = $taskStats['completed_my_count'] ?? 0;
    @endphp

    <section class="tasks-shell">
        <div id="tasksAdminConfig" data-task-base-url="{{ url('/tasks') }}"></div>
        <div class="tasks-hero">
            <div class="hero-copy">
                <p class="eyebrow">Work Allocation</p>
                <h3>Assign, track, and review team tasks from one EMS workspace.</h3>
                <p>
                    @if($role === 'admin')
                        You can assign tasks to HR, managers, and employees, then monitor submissions and progress from one page.
                    @else
                        You can assign tasks to employees, monitor updates, and keep the team workflow organized.
                    @endif
                </p>
            </div>
            <div class="hero-side">
                <div class="hero-chip">
                    <span class="hero-chip-label">Assigned By Me</span>
                    <strong>{{ $assignedCount }}</strong>
                </div>
                <div class="hero-chip muted-chip">
                    <span class="hero-chip-label">My Tasks</span>
                    <strong>{{ $myCount }}</strong>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success task-alert">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger task-alert">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger task-alert">{{ $errors->first() }}</div>
        @endif

        <div class="stats-grid">
            <article class="metric-card">
                <div class="metric-icon"><i class="bi bi-person-workspace"></i></div>
                <div>
                    <span class="metric-label">Assigned By Me</span>
                    <h4>{{ $assignedCount }}</h4>
                    <p>Tasks created for your team</p>
                </div>
            </article>
            <article class="metric-card">
                <div class="metric-icon"><i class="bi bi-hourglass-split"></i></div>
                <div>
                    <span class="metric-label">My Pending</span>
                    <h4>{{ $pendingMyCount }}</h4>
                    <p>Tasks you still need to update</p>
                </div>
            </article>
            <article class="metric-card">
                <div class="metric-icon"><i class="bi bi-check2-circle"></i></div>
                <div>
                    <span class="metric-label">My Completed</span>
                    <h4>{{ $completedMyCount }}</h4>
                    <p>Tasks you already finished</p>
                </div>
            </article>
        </div>

        <div class="tasks-grid">
            <section class="panel-card form-panel">
                <div class="panel-head">
                    <div>
                        <p class="panel-kicker">Assign Task</p>
                        <h4>Create a new task</h4>
                    </div>
                    <span class="panel-note">
                        @if($role === 'admin')
                            You can assign tasks to HR, managers, and employees.
                        @else
                            You can assign tasks to employees from your team.
                        @endif
                    </span>
                </div>

                <form action="{{ route('tasks.store') }}" method="POST" class="task-form">
                    @csrf
                    <div class="grid-2">
                        <div class="form-floating">
                            <select name="assigned_to" id="assignedTo" class="form-select" required>
                                <option value="">Select Team Member</option>
                                @foreach($assignableUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role }})</option>
                                @endforeach
                            </select>
                            <label for="assignedTo">Assign To</label>
                        </div>

                        <div class="form-floating">
                            <input type="date" name="due_date" id="dueDate" class="form-control" required>
                            <label for="dueDate">Due Date</label>
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="form-floating">
                            <input type="text" name="title" id="taskTitle" class="form-control" placeholder="Task Title" required>
                            <label for="taskTitle">Task Title</label>
                        </div>

                        <div class="form-floating">
                            <select name="priority" id="taskPriority" class="form-select" required>
                                <option value="Low">Low</option>
                                <option value="Medium" selected>Medium</option>
                                <option value="High">High</option>
                            </select>
                            <label for="taskPriority">Priority</label>
                        </div>
                    </div>

                    <div class="form-floating textarea-wrap">
                        <textarea name="description" id="taskDescription" class="form-control textarea-control" placeholder="Task Details" required></textarea>
                        <label for="taskDescription">Task Details</label>
                    </div>

                    <button type="submit" class="btn primary-btn">Assign Task</button>
                </form>
            </section>

            <section class="panel-card table-panel">
                <div class="table-toolbar">
                    <div>
                        <p class="panel-kicker">My Tasks</p>
                        <h4 class="mb-0">Assigned to me</h4>
                    </div>
                    <div class="table-search">
                        <i class="bi bi-search"></i>
                        <input type="text" id="myTasksSearch" placeholder="Search my tasks">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table task-table align-middle mb-0" id="myTasksTable">
                        <thead>
                            <tr>
                                <th>Task</th>
                                <th>Assigned By</th>
                                <th>Priority</th>
                                <th>Due</th>
                                <th>Status</th>
                                <th>Submit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($myTasks as $task)
                                <tr>
                                    <td>
                                        <strong>{{ $task->title }}</strong>
                                        <small class="d-block text-muted">{{ $task->description }}</small>
                                    </td>
                                    <td>{{ $task->assignedBy->name ?? '-' }}</td>
                                    <td><span class="priority-badge priority-{{ strtolower($task->priority) }}">{{ $task->priority }}</span></td>
                                    <td>{{ optional($task->due_date)->format('d M Y') }}</td>
                                    <td><span class="status-badge status-{{ strtolower(str_replace(' ', '-', $task->status)) }}">{{ $task->status }}</span></td>
                                    <td>
                                        <button
                                            type="button"
                                            class="btn action-btn submit-btn task-submit-trigger"
                                            data-id="{{ $task->id }}"
                                            data-title="{{ $task->title }}"
                                            data-note="{{ $task->submission_note }}"
                                            data-status="{{ $task->status }}"
                                        >
                                            Submit Update
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="empty-state">
                                            <i class="bi bi-clipboard2-x"></i>
                                            <h5>No tasks assigned yet</h5>
                                            <p>Your assigned tasks will appear here when a manager creates one for you.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @if ($myTasks instanceof \Illuminate\Contracts\Pagination\Paginator && $myTasks->hasPages())
                <div class="pagination-shell">
                    {{ $myTasks->links('pagination::bootstrap-5') }}
                </div>
            @endif
            </section>
        </div>

        <section class="panel-card table-panel">
            <div class="table-toolbar">
                <div>
                    <p class="panel-kicker">Assigned By Me</p>
                    <h4 class="mb-0">Team task list</h4>
                </div>
                <div class="table-search">
                    <i class="bi bi-search"></i>
                    <input type="text" id="assignedTasksSearch" placeholder="Search assigned tasks">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table task-table align-middle mb-0" id="assignedTasksTable">
                    <thead>
                        <tr>
                            <th>Task</th>
                            <th>Assigned To</th>
                            <th>Priority</th>
                            <th>Due</th>
                            <th>Status</th>
                            <th>Submission Detail</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignedTasks as $task)
                            <tr>
                                <td>
                                    <strong>{{ $task->title }}</strong>
                                    <small class="d-block text-muted">{{ $task->description }}</small>
                                </td>
                                <td>{{ $task->assignedTo->name ?? '-' }} <small class="d-block text-muted">{{ $task->assignedTo->role ?? '' }}</small></td>
                                <td><span class="priority-badge priority-{{ strtolower($task->priority) }}">{{ $task->priority }}</span></td>
                                <td>{{ optional($task->due_date)->format('d M Y') }}</td>
                                <td><span class="status-badge status-{{ strtolower(str_replace(' ', '-', $task->status)) }}">{{ $task->status }}</span></td>
                                <td>{{ $task->submission_note ?: 'No update submitted yet' }}</td>
                                <td>
                                    <div class="action-group">
                                        <button
                                            type="button"
                                            class="btn action-btn edit-btn task-edit-trigger"
                                            data-id="{{ $task->id }}"
                                            data-assigned-to="{{ $task->assigned_to }}"
                                            data-title="{{ $task->title }}"
                                            data-description="{{ $task->description }}"
                                            data-priority="{{ $task->priority }}"
                                            data-due-date="{{ optional($task->due_date)->format('Y-m-d') }}"
                                        >
                                            Edit
                                        </button>
                                        <form method="POST" action="{{ route('tasks.destroy', $task->id) }}" onsubmit="return confirm('Delete this task?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn action-btn delete-btn">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="bi bi-journal-x"></i>
                                        <h5>No assigned tasks yet</h5>
                                        <p>Tasks you create for your team will appear here.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($assignedTasks instanceof \Illuminate\Contracts\Pagination\Paginator && $assignedTasks->hasPages())
                <div class="pagination-shell">
                    {{ $assignedTasks->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </section>
    </section>

    <div class="task-modal" id="taskSubmitModal">
        <div class="task-modal-card">
            <div class="panel-head">
                <div>
                    <p class="panel-kicker">Submit Task Update</p>
                    <h4 id="taskSubmitTitle">Task Update</h4>
                </div>
                <button type="button" class="modal-close" id="taskModalClose">&times;</button>
            </div>

            <form method="POST" id="taskSubmitForm">
                @csrf
                @method('PUT')

                <div class="grid-2">
                    <div class="form-floating">
                        <select name="status" id="taskSubmitStatus" class="form-select" required>
                            <option value="In Progress">In Progress</option>
                            <option value="Submitted">Submitted</option>
                            <option value="Completed">Completed</option>
                        </select>
                        <label for="taskSubmitStatus">Work Status</label>
                    </div>
                </div>

                <div class="form-floating textarea-wrap">
                    <textarea name="submission_note" id="taskSubmissionNote" class="form-control textarea-control" placeholder="Submission Note" required></textarea>
                    <label for="taskSubmissionNote">Submission Detail</label>
                </div>

                <div class="action-row">
                    <button type="submit" class="btn primary-btn">Submit Task Update</button>
                </div>
            </form>
        </div>
    </div>

    <div class="task-modal" id="taskEditModal">
        <div class="task-modal-card">
            <div class="panel-head">
                <div>
                    <p class="panel-kicker">Edit Task</p>
                    <h4 id="taskEditTitle">Update assigned task</h4>
                </div>
                <button type="button" class="modal-close" id="taskEditModalClose">&times;</button>
            </div>

            <form method="POST" id="taskEditForm">
                @csrf
                @method('PUT')

                <div class="grid-2">
                    <div class="form-floating">
                        <select name="assigned_to" id="editAssignedTo" class="form-select" required>
                            <option value="">Select Team Member</option>
                            @foreach($assignableUsers as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role }})</option>
                            @endforeach
                        </select>
                        <label for="editAssignedTo">Assign To</label>
                    </div>

                    <div class="form-floating">
                        <input type="date" name="due_date" id="editDueDate" class="form-control" required>
                        <label for="editDueDate">Due Date</label>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-floating">
                        <input type="text" name="title" id="editTaskTitleInput" class="form-control" placeholder="Task Title" required>
                        <label for="editTaskTitleInput">Task Title</label>
                    </div>

                    <div class="form-floating">
                        <select name="priority" id="editTaskPriority" class="form-select" required>
                            <option value="Low">Low</option>
                            <option value="Medium">Medium</option>
                            <option value="High">High</option>
                        </select>
                        <label for="editTaskPriority">Priority</label>
                    </div>
                </div>

                <div class="form-floating textarea-wrap">
                    <textarea name="description" id="editTaskDescription" class="form-control textarea-control" placeholder="Task Details" required></textarea>
                    <label for="editTaskDescription">Task Details</label>
                </div>

                <div class="action-row">
                    <button type="submit" class="btn primary-btn">Save Task Changes</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/tasks-admin.js') }}"></script>
@endsection
