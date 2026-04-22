<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Salary;
use App\Models\User;
use App\Models\Attendance;
use App\Notifications\SystemStatusNotification;
use App\Services\SmsService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class SalaryController extends Controller
{
    protected function parseSalaryMonth(string $value): Carbon
    {
        try {
            return Carbon::createFromFormat('Y-m', $value)->startOfMonth();
        } catch (\Throwable $e) {
            return Carbon::parse($value . '-01')->startOfMonth();
        }
    }

    protected function buildAttendanceSalaryData(User $user, string $month): array
    {
        $monthDate = $this->parseSalaryMonth($month);
        $presentDays = Attendance::where('user_id', $user->id)
            ->whereYear('date', $monthDate->year)
            ->whereMonth('date', $monthDate->month)
            ->where('status', 'Present')
            ->count();

        $dailyRate = (float) ($user->basic_salary ?? 0);
        $attendanceSalary = round($presentDays * $dailyRate, 2);

        return [
            'month_value' => $monthDate->format('Y-m'),
            'month_label' => $monthDate->format('F Y'),
            'present_days' => $presentDays,
            'daily_rate' => $dailyRate,
            'attendance_salary' => $attendanceSalary,
        ];
    }

    public function admin()
    {
        $role = strtolower(trim(auth()->user()->role ?? ''));

        if (!in_array($role, ['admin', 'hr', 'manager'])) {
            return redirect()->route('salary-user')->with('error', 'Access denied');
        }

        $salaries = Salary::with('user')->latest()->paginate(10);
        $users = User::whereIn('role', ['Employee', 'HR', 'Manager'])->get();
        $salaryStats = [
            'total' => Salary::count(),
            'paid' => Salary::where('status', 'Paid')->count(),
            'pending' => Salary::where('status', 'Pending')->count(),
        ];

        return view('salary-admin', compact('salaries', 'users', 'salaryStats'));
    }

    public function preview(Request $request)
    {
        $role = strtolower(trim(auth()->user()->role ?? ''));

        if (!in_array($role, ['admin', 'hr', 'manager'])) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'month' => 'required',
        ]);

        $user = User::findOrFail($request->user_id);
        $data = $this->buildAttendanceSalaryData($user, $request->month);

        return response()->json($data);
    }

    public function user()
    {
        $salaries = Salary::where('user_id', auth()->id())->with('user')->latest()->paginate(10);
        $salaryStats = [
            'total' => Salary::where('user_id', auth()->id())->count(),
            'paid' => Salary::where('user_id', auth()->id())->where('status', 'Paid')->count(),
            'pending' => Salary::where('user_id', auth()->id())->where('status', 'Pending')->count(),
        ];

        return view('salary-user', compact('salaries', 'salaryStats'));
    }

    public function store(Request $request, SmsService $smsService)
{
    $role = strtolower(trim(auth()->user()->role ?? ''));

    if (!in_array($role, ['admin', 'hr', 'manager'])) {
        return redirect()->route('salary-user')->with('error', 'Access denied');
    }

    $request->validate([
        'user_id' => 'required|exists:users,id',
        'month' => 'required',
        'daily_rate' => 'nullable|numeric|min:0',
        'bonus' => 'nullable|numeric',
        'deduction' => 'nullable|numeric',
        'status' => 'required',
    ]);

    $user = User::findOrFail($request->user_id);
    $salaryData = $this->buildAttendanceSalaryData($user, $request->month);
    $dailyRate = $request->filled('daily_rate')
        ? (float) $request->daily_rate
        : (float) $salaryData['daily_rate'];
    $basic = round($salaryData['present_days'] * $dailyRate, 2);
    $bonus = (float) ($request->bonus ?? 0);
    $deduction = (float) ($request->deduction ?? 0);
    $total = $basic + $bonus - $deduction;

    $salary = Salary::create([
        'user_id' => $request->user_id,
        'month' => $salaryData['month_value'],
        'daily_rate' => $dailyRate,
        'present_days' => $salaryData['present_days'],
        'basic_salary' => $basic,
        'bonus' => $bonus,
        'deduction' => $deduction,
        'net_salary' => $total,
        'status' => $request->status,
    ]);

    if ($user) {
        $user->notify(new SystemStatusNotification(
            'Salary ' . ucfirst(strtolower($request->status)),
            'Your salary for ' . $salaryData['month_label'] . ' has been marked as ' . strtolower($request->status) . '. Net salary: ' . $salary->net_salary . '.',
            strtolower($request->status) === 'paid' ? 'success' : 'warning',
            route('salary-slip', $salary->id)
        ));
    }

    // if ($user && !empty($user->phone) && strtolower($request->status) === 'paid') {
    //     $message = "Dear {$user->name}, your salary for {$request->month} has been paid. Net salary: {$salary->net_salary}.";
    //     $smsService->sendSms($user->phone, $message);
    // }

    return redirect()->route('salary-admin')->with('success', 'Salary added successfully from attendance calculation.');
}


    public function slip($id)
    {
        $salary = Salary::with('user')->findOrFail($id);

        $role = strtolower(trim(auth()->user()->role ?? ''));

        if ($role === 'employee' && $salary->user_id != auth()->id()) {
            return redirect()->route('salary-user')->with('error', 'Access denied');
        }

        return view('salary-slip', compact('salary'));
    }

    public function downloadSlip($id)
    {
        $salary = Salary::with('user')->findOrFail($id);

        $role = strtolower(trim(auth()->user()->role ?? ''));

        if ($role === 'employee' && $salary->user_id != auth()->id()) {
            return redirect()->route('salary-user')->with('error', 'Access denied');
        }

        $userName = $salary->user->name ?? 'employee';
        $userName = strtolower(str_replace(' ', '_', $userName));

        $month = Carbon::parse($salary->month . '-01')->format('F_Y');
$month = strtolower($month);

        $fileName = $userName . '_' . $month . '.pdf';

        $pdf = Pdf::loadView('salary-slip', compact('salary'));

        return $pdf->download($fileName);
    }
}
