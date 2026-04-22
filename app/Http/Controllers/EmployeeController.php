<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;

class EmployeeController extends Controller
{
    public function index()
    {
        $role = strtolower(trim(auth()->user()->role ?? ''));
        $canManageEmployees = in_array($role, ['admin', 'hr']);

        $employeeQuery = Employee::with(['user', 'department'])->latest();

        if (!$canManageEmployees) {
            $employeeQuery->where('user_id', auth()->id());
        }

        $employees = (clone $employeeQuery)->paginate(10);

        $users = $canManageEmployees
            ? User::whereIn('role', ['Employee', 'HR', 'Manager'])->orderBy('name')->get()
            : collect();
        $departments = $canManageEmployees
            ? Department::orderBy('name')->get()
            : collect();

        $stats = [
            'total_records' => (clone $employeeQuery)->count(),
            'assigned_departments' => (clone $employeeQuery)->whereNotNull('department_id')->distinct('department_id')->count('department_id'),
            'scheduled_shifts' => (clone $employeeQuery)->whereNotNull('schedule_type')->count(),
        ];

        return view('employee-details', compact('employees', 'users', 'departments', 'stats'));
    }

    public function store(Request $request)
    {
        $role = strtolower(trim(auth()->user()->role ?? ''));

        if (!in_array($role, ['admin', 'hr'])) {
            return back()->with('error', 'Only admin or HR can assign or change shifts.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
        
            'department_id' => 'nullable|exists:departments,id',
            'designation' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
            'address' => 'nullable|string',
            'basic_salary' => 'nullable|numeric|min:0',
            'schedule_type' => 'required|string|in:Morning,General,Evening,Night,Custom',
            'shift_start' => 'nullable|date_format:H:i',
            'shift_end' => 'nullable|date_format:H:i',
        ]);

        [$shiftStart, $shiftEnd] = $this->resolveShiftTimes(
            $request->schedule_type,
            $request->shift_start,
            $request->shift_end
        );

        $user = User::findOrFail($request->user_id);

$existingEmployee = Employee::where('user_id', $request->user_id)->first();

if ($existingEmployee) {
    $employeeId = $existingEmployee->employee_id;
} else {
    $number = str_pad($user->id, 3, '0', STR_PAD_LEFT);
    $name = strtoupper(str_replace(' ', '', $user->name));
    $employeeId = 'EMP' . $number . '_' . $name;
}


        Employee::updateOrCreate(
            ['user_id' => $request->user_id],
            [
                'employee_id' => $employeeId,
                'department_id' => $request->department_id,
                'designation' => $request->designation,
                'dob' => $request->dob,
                'address' => $request->address,
                'basic_salary' => $request->basic_salary ?? 0,
                'schedule_type' => $request->schedule_type,
                'shift_start' => $shiftStart,
                'shift_end' => $shiftEnd,
            ]
        );

        return back()->with('success', 'Employee shift saved successfully.');
    }

    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $role = strtolower(trim(auth()->user()->role ?? ''));

        if (!in_array($role, ['admin', 'hr'])) {
            return back()->with('error', 'Only admin or HR can delete shift records.');
        }

        $employee->delete();

        return back()->with('success', 'Employee shift record deleted successfully.');
    }

    private function resolveShiftTimes(string $scheduleType, ?string $shiftStart, ?string $shiftEnd): array
    {
        $defaults = [
            'Morning' => ['06:00', '14:00'],
            'General' => ['09:00', '17:00'],
            'Evening' => ['14:00', '22:00'],
            'Night' => ['22:00', '06:00'],
        ];

        if ($scheduleType === 'Custom') {
            return [$shiftStart, $shiftEnd];
        }

        return $defaults[$scheduleType] ?? [null, null];
    }
}
