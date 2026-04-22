<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::withCount('employees')
            ->latest()
            ->paginate(10);

        $stats = [
            'total_departments' => Department::count(),
            'total_employees' => Department::withCount('employees')->get()->sum('employees_count'),
            'largest_department' => optional(Department::withCount('employees')->orderByDesc('employees_count')->first())->name ?? 'No Departments',
            'leadership_coverage' => Department::whereNotNull('head')->where('head', '!=', '')->count(),
        ];

        return view('departments', compact('departments', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
            'head' => 'nullable|string|max:255',
        ]);

        Department::create([
            'name' => $request->name,
            'head' => $request->head,
        ]);

        return back()->with('success', 'Department added successfully.');
    }

    public function update(Request $request, $id)
    {
        $department = Department::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
            'head' => 'nullable|string|max:255',
        ]);

        $department->update([
            'name' => $request->name,
            'head' => $request->head,
        ]);

        return back()->with('success', 'Department updated successfully.');
    }

    public function destroy($id)
    {
        $department = Department::findOrFail($id);
        $department->delete();

        return back()->with('success', 'Department deleted successfully.');
    }
}
