<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveModel;
use App\Models\Salary;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $currentUser = auth()->user();
        $role = strtolower(trim($currentUser->role ?? ''));

        if ($currentUser->force_password_change) {
            return redirect()->route('password.change.form');
        }

        if ($role === 'employee') {
            $users = User::where('id', $currentUser->id)->get();
            $employees = Employee::with(['user', 'department'])
                ->where('user_id', $currentUser->id)
                ->latest()
                ->get();

            return view('dashboard', [
                'userCount' => 1,
                'departmentCount' => $currentUser->department ? 1 : 0,
                'employeeCount' => $employees->count(),
                'leaveCount' => LeaveModel::where('user_id', $currentUser->id)->count(),
                'salaryCount' => Salary::where('user_id', $currentUser->id)->count(),
                'scheduledShiftCount' => $employees->whereNotNull('schedule_type')->count(),
                'pendingLeaveCount' => LeaveModel::where('user_id', $currentUser->id)->where('status', 'Pending')->count(),
                'paidSalaryCount' => Salary::where('user_id', $currentUser->id)->where('status', 'Paid')->count(),
                'users' => $users,
                'employees' => $employees,
            ]);
        }

        $users = User::latest()->take(8)->get();
        $employees = Employee::with(['user', 'department'])->latest()->take(8)->get();

        return view('dashboard', [
            'userCount' => User::count(),
            'departmentCount' => Department::count(),
            'employeeCount' => Employee::count(),
            'leaveCount' => LeaveModel::count(),
            'salaryCount' => Salary::count(),
            'scheduledShiftCount' => Employee::whereNotNull('schedule_type')->count(),
            'pendingLeaveCount' => LeaveModel::where('status', 'Pending')->count(),
            'paidSalaryCount' => Salary::where('status', 'Paid')->count(),
            'users' => $users,
            'employees' => $employees,
        ]);
    }
}
