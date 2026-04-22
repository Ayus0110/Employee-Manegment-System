<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use App\Notifications\SystemStatusNotification;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function admin(Request $request)
    {
        if (!in_array(strtolower(auth()->user()->role), ['admin', 'hr', 'manager'])) {
            return redirect('/dashboard')->with('error', 'Access Denied');
        }

        $selectedDate = $request->filled('date')
            ? Carbon::parse($request->date)->toDateString()
            : Carbon::today()->toDateString();

        $attendanceQuery = Attendance::with('user')
            ->whereDate('date', $selectedDate)
            ->latest();

        $attendances = (clone $attendanceQuery)->paginate(10)->withQueryString();

        $employees = User::where('role', 'Employee')->get();
        $attendanceStats = [
            'present' => (clone $attendanceQuery)->where('status', 'Present')->count(),
            'absent' => (clone $attendanceQuery)->where('status', 'Absent')->count(),
        ];

        return view('attendance-admin', compact('attendances', 'employees', 'attendanceStats', 'selectedDate'));
    }

    public function user()
    {
        $attendanceQuery = Attendance::where('user_id', auth()->id())->latest();
        $attendances = (clone $attendanceQuery)->paginate(10);
        $attendanceStats = [
            'total' => (clone $attendanceQuery)->count(),
            'present' => (clone $attendanceQuery)->where('status', 'Present')->count(),
            'absent' => (clone $attendanceQuery)->where('status', 'Absent')->count(),
        ];

        return view('attendance-user', compact('attendances', 'attendanceStats'));
    }

    public function record(Request $request)
    {
        $role = strtolower(trim(auth()->user()->role ?? ''));

        $month = (int) ($request->month ?? now()->month);
        $year = (int) ($request->year ?? now()->year);

        if ($role === 'employee') {
            $selectedUser = auth()->user();
        } else {
            $selectedUserId = $request->user_id ?? auth()->id();
            $selectedUser = User::find($selectedUserId) ?? auth()->user();
        }

        $employees = [];
        if (in_array($role, ['admin', 'hr', 'manager'])) {
            $employees = User::whereIn('role', ['Employee', 'HR', 'Manager'])->get();
        }

        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;

        $attendanceData = Attendance::where('user_id', $selectedUser->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->date)->day;
            });

        $calendar = [];
        $presentCount = 0;
        $absentCount = 0;
        $noneCount = 0;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($year, $month, $day);
            $status = 'None';

            if ($date->isSunday()) {
                $status = 'WO';
            } elseif (isset($attendanceData[$day])) {
                $status = $attendanceData[$day]->status;
            }

            if ($status === 'Present') {
                $presentCount++;
            } elseif ($status === 'Absent') {
                $absentCount++;
            } elseif ($status === 'None') {
                $noneCount++;
            }

            $calendar[] = [
                'day' => $day,
                'weekday' => $date->format('D'),
                'status' => $status,
            ];
        }

        return view('attendance-record', compact(
            'month',
            'year',
            'selectedUser',
            'employees',
            'calendar',
            'presentCount',
            'absentCount',
            'noneCount'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:Present,Absent',
            'date' => 'required|date',
        ]);

        $attendanceDate = Carbon::parse($request->date)->toDateString();

        $attendance = Attendance::updateOrCreate(
            [
                'user_id' => $request->user_id,
                'date' => $attendanceDate,
            ],
            [
                'status' => $request->status,
            ]
        );

        $user = User::find($request->user_id);

        if ($user) {
            $user->notify(new SystemStatusNotification(
                'Attendance marked',
                'Your attendance for ' . Carbon::parse($attendanceDate)->format('d M Y') . ' was marked as ' . $attendance->status . '.',
                strtolower($attendance->status) === 'present' ? 'success' : 'warning',
                route('attendance-user')
            ));
        }

        return redirect()->route('attendance-admin', ['date' => $attendanceDate])->with('success', 'Attendance saved successfully.');
    }
}
