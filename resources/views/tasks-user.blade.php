@extends('layouts.app')

@section('title', 'My Tasks')
@section('page_title', 'My Tasks')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/tasks.css') }}">
@endsection

@section('content')
    @php
        $myCount = $taskStats['my_count'] ?? 0;
        $pendingMyCount = $taskStats['pending_my_count'] ?? 0;
        $completedMyCount = $taskStats['completed_my_count'] ?? 0;
    @endphp

    <section class="tasks-shell">
        <div id="tasksUserConfig" data-task-base-url="{{ url('/tasks') }}"></div>
        <div class="tasks-hero">
            <div class="hero-copy">
                <p class="eyebrow">My Workboard</p>
                <h3>Track your assigned tasks and submit progress updates.</h3>
                <p>Review due dates, update the current work status, and submit task details directly from your employee dashboard.</p>
            </div>
            <div class="hero-side">
                <div class="hero-chip">
                    <span class="hero-chip-label">My Tasks</span>
                    <strong>{{ $myCount }}</strong>
                </div>
                <div class="hero-chip muted-chip">
                    <span class="hero-chip-label">Pending</span>
                    <strong>{{ $pendingMyCount }}</strong>
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
                <div class="metric-icon"><i class="bi bi-list-task"></i></div>
                <div>
                    <span class="metric-label">All Tasks</span>
                    <h4>{{ $myCount }}</h4>
                    <p>Tasks assigned to you</p>
                </div>
            </article>
            <article class="metric-card">
                <div class="metric-icon"><i class="bi bi-hourglass-split"></i></div>
                <div>
                    <span class="metric-label">Pending</span>
                    <h4>{{ $pendingMyCount }}</h4>
                    <p>Need progress updates</p>
                </div>
            </article>
            <article class="metric-card">
                <div class="metric-icon"><i class="bi bi-check2-circle"></i></div>
                <div>
                    <span class="metric-label">Completed</span>
                    <h4>{{ $completedMyCount }}</h4>
                    <p>Finished submissions</p>
                </div>
            </article>
        </div>

        <section class="panel-card table-panel">
            <div class="table-toolbar">
                <div>
                    <p class="panel-kicker">My Task List</p>
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
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/tasks-user.js') }}"></script>
@endsection
