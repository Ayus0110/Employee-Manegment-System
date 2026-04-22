<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Notifications\SystemStatusNotification;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $role = strtolower(trim(auth()->user()->role ?? ''));
        $canAssign = in_array($role, ['admin', 'hr', 'manager']);

        $assignableUsers = collect();

        if ($role === 'admin') {
            $assignableUsers = User::whereIn('role', ['Employee', 'HR', 'Manager'])
                ->where('id', '!=', auth()->id())
                ->orderBy('name')
                ->get();
        } elseif (in_array($role, ['hr', 'manager'])) {
            $assignableUsers = User::where('role', 'Employee')
                ->orderBy('name')
                ->get();
        }

        $assignedTaskQuery = Task::with(['assignedTo', 'assignedBy'])
            ->where('assigned_by', auth()->id())
            ->latest();

        $myTaskQuery = Task::with(['assignedTo', 'assignedBy'])
            ->where('assigned_to', auth()->id())
            ->latest();
          // pagination
        $assignedTasks = $canAssign
            ? (clone $assignedTaskQuery)->paginate(10, ['*'], 'assigned_page')
            : collect();

        $myTasks = (clone $myTaskQuery)->paginate(10, ['*'], 'my_page');

        $taskStats = [
            'assigned_count' => $canAssign ? (clone $assignedTaskQuery)->count() : 0,
            'my_count' => (clone $myTaskQuery)->count(),
            'pending_my_count' => (clone $myTaskQuery)->whereIn('status', ['Assigned', 'In Progress'])->count(),
            'completed_my_count' => (clone $myTaskQuery)->where('status', 'Completed')->count(),
        ];

        $view = $canAssign ? 'tasks-admin' : 'tasks-user';

        return view($view, [
            'role' => $role,
            'canAssign' => $canAssign,
            'assignableUsers' => $assignableUsers,
            'assignedTasks' => $assignedTasks,
            'myTasks' => $myTasks,
            'taskStats' => $taskStats,
        ]);
    }

    public function store(Request $request)
    {
        $role = strtolower(trim(auth()->user()->role ?? ''));

        if (!in_array($role, ['admin', 'hr', 'manager'])) {
            return back()->with('error', 'Access denied.');
        }

        $request->validate([
            'assigned_to' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:Low,Medium,High',
            'due_date' => 'required|date',
        ]);

        $assignee = User::findOrFail($request->assigned_to);
        $assigneeRole = strtolower(trim($assignee->role ?? ''));

        if ($role === 'admin' && !in_array($assigneeRole, ['employee', 'hr', 'manager'])) {
            return back()->with('error', 'Admin can assign tasks only to HR, Manager, or Employee.');
        }

        if (in_array($role, ['hr', 'manager']) && $assigneeRole !== 'employee') {
            return back()->with('error', ucfirst($role) . ' can assign tasks only to employees.');
        }

        $task = Task::create([
            'assigned_by' => auth()->id(),
            'assigned_to' => $assignee->id,
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'due_date' => $request->due_date,
            'status' => 'Assigned',
        ]);

        $assignee->notify(new SystemStatusNotification(
            'New task assigned',
            auth()->user()->name . ' assigned you a new task: ' . $task->title . '.',
            'task',
            route('tasks')
        ));

        return back()->with('success', 'Task assigned successfully.');
    }

    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        if ($task->assigned_by !== auth()->id()) {
            return back()->with('error', 'You can only edit tasks assigned by you.');
        }

        $role = strtolower(trim(auth()->user()->role ?? ''));

        $request->validate([
            'assigned_to' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:Low,Medium,High',
            'due_date' => 'required|date',
        ]);

        $assignee = User::findOrFail($request->assigned_to);
        $assigneeRole = strtolower(trim($assignee->role ?? ''));

        if ($role === 'admin' && !in_array($assigneeRole, ['employee', 'hr', 'manager'])) {
            return back()->with('error', 'Admin can assign tasks only to HR, Manager, or Employee.');
        }

        if (in_array($role, ['hr', 'manager']) && $assigneeRole !== 'employee') {
            return back()->with('error', ucfirst($role) . ' can assign tasks only to employees.');
        }

        $task->update([
            'assigned_to' => $assignee->id,
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'due_date' => $request->due_date,
        ]);

        $assignee->notify(new SystemStatusNotification(
            'Task updated',
            auth()->user()->name . ' updated the task: ' . $task->title . '.',
            'task',
            route('tasks')
        ));

        return back()->with('success', 'Task updated successfully.');
    }

    public function submit(Request $request, $id)
    {
        $task = Task::with('assignedBy')->findOrFail($id);

        if ($task->assigned_to !== auth()->id()) {
            return back()->with('error', 'You can only submit your own task.');
        }

        $request->validate([
            'submission_note' => 'required|string',
            'status' => 'required|in:In Progress,Submitted,Completed',
        ]);

        $task->update([
            'submission_note' => $request->submission_note,
            'status' => $request->status,
            'submitted_at' => now(),
        ]);

        if ($task->assignedBy) {
            $task->assignedBy->notify(new SystemStatusNotification(
                'Task update received',
                auth()->user()->name . ' updated the task "' . $task->title . '" with status ' . $task->status . '.',
                'task',
                route('tasks')
            ));
        }

        return back()->with('success', 'Task update submitted successfully.');
    }

    public function destroy($id)
    {
        $task = Task::findOrFail($id);

        if ($task->assigned_by !== auth()->id()) {
            return back()->with('error', 'You can only delete tasks assigned by you.');
        }

        $task->delete();

        return back()->with('success', 'Task deleted successfully.');
    }
}
