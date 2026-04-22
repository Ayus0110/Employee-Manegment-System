<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveModel;
use App\Models\User;
use App\Notifications\SystemStatusNotification;
use App\Services\SmsService;
use Illuminate\Support\Facades\DB;

class LeaveController extends Controller
{
    protected array $leaveBalanceColumns = [
        'Casual Leave' => 'casual_leave_balance',
        'Sick Leave' => 'sick_leave_balance',
        'Paid Leave' => 'paid_leave_balance',
    ];

    protected function getLeaveBalance(User $user, string $type): float
    {
        $column = $this->leaveBalanceColumns[$type] ?? null;

        return $column ? (float) ($user->{$column} ?? 0) : 0;
    }

    protected function setLeaveBalance(User $user, string $type, float $value): void
    {
        $column = $this->leaveBalanceColumns[$type] ?? null;

        if ($column) {
            $user->{$column} = max($value, 0);
        }
    }

    public function admin()
    {
        $role = strtolower(trim(auth()->user()->role ?? ''));

        if (!in_array($role, ['admin', 'hr', 'manager'])) {
            return redirect()->route('leave-user')->with('error', 'Access denied');
        }

        $leaveQuery = LeaveModel::with('user')->latest();
        $leaves = (clone $leaveQuery)->paginate(10);
        $leaveStats = [
            'total' => LeaveModel::count(),
            'pending' => LeaveModel::where('status', 'Pending')->count(),
            'approved' => LeaveModel::where('status', 'Approved')->count(),
            'rejected' => LeaveModel::where('status', 'Rejected')->count(),
        ];

        return view('leave-admin', compact('leaves', 'leaveStats'));
    }

    public function user(Request $request)
    {
        $leaveQuery = LeaveModel::where('user_id', auth()->id())->latest();
        $leaves = (clone $leaveQuery)->paginate(10)->withQueryString();
        $currentUser = auth()->user();
        $leaveStats = [
            'total' => LeaveModel::where('user_id', auth()->id())->count(),
            'pending' => LeaveModel::where('user_id', auth()->id())->where('status', 'Pending')->count(),
            'approved' => LeaveModel::where('user_id', auth()->id())->where('status', 'Approved')->count(),
            'rejected' => LeaveModel::where('user_id', auth()->id())->where('status', 'Rejected')->count(),
            'balances' => [
                'Casual Leave' => $this->getLeaveBalance($currentUser, 'Casual Leave'),
                'Sick Leave' => $this->getLeaveBalance($currentUser, 'Sick Leave'),
                'Paid Leave' => $this->getLeaveBalance($currentUser, 'Paid Leave'),
            ],
        ];

        $editLeave = null;
        if ($request->filled('edit_id')) {
            $editLeave = LeaveModel::where('id', $request->edit_id)
                ->where('user_id', auth()->id())
                ->where('status', 'Pending')
                ->first();
        }

        return view('leave-user', compact('leaves', 'editLeave', 'leaveStats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'days' => 'required|integer|min:1',
            'reason' => 'required|string',
            'type' => 'required|in:Casual Leave,Sick Leave,Paid Leave',
        ]);

        $balance = $this->getLeaveBalance(auth()->user(), $request->type);
        if ((float) $request->days > $balance) {
            return back()->with('error', 'Not enough ' . strtolower($request->type) . ' balance. Available balance: ' . $balance . ' day(s).')->withInput();
        }

        $leave = LeaveModel::create([
            'user_id' => auth()->id(),
            'from_date' => $request->date,
            'to_date' => $request->date,
            'days' => $request->days,
            'reason' => $request->reason,
            'type' => $request->type,
            'status' => 'Pending',
        ]);

        User::whereIn('role', ['Admin', 'HR', 'Manager'])
            ->where('id', '!=', auth()->id())
            ->get()
            ->each(function ($admin) use ($leave) {
                $admin->notify(new SystemStatusNotification(
                    'New leave request',
                    auth()->user()->name . ' submitted a leave request for ' . $leave->days . ' day(s).',
                    'leave',
                    route('leave-admin')
                ));
            });

        return back()->with('success', 'Leave applied successfully');
    }

 public function updateStatus(Request $request, $id, SmsService $smsService)
{
    $role = strtolower(trim(auth()->user()->role ?? ''));

    if (!in_array($role, ['admin', 'hr', 'manager'])) {
        return redirect()->back()->with('error', 'Unauthorized access');
    }

    $request->validate([
        'status' => 'required|in:Approved,Rejected',
    ]);

    $leave = LeaveModel::with('user')->findOrFail($id);

    if (strtolower(trim($leave->status)) != 'pending') {
        return redirect()->back()->with('error', 'Status already updated');
    }

    if ($request->status === 'Approved' && $leave->user) {
        $availableBalance = $this->getLeaveBalance($leave->user, $leave->type);

        if ((float) $leave->days > $availableBalance) {
            return redirect()->back()->with('error', $leave->user->name . ' does not have enough ' . strtolower($leave->type) . ' balance. Available: ' . $availableBalance . ' day(s).');
        }
    }

    DB::transaction(function () use ($leave, $request) {
        if ($request->status === 'Approved' && $leave->user) {
            $remainingBalance = $this->getLeaveBalance($leave->user, $leave->type) - (float) $leave->days;
            $this->setLeaveBalance($leave->user, $leave->type, $remainingBalance);
            $leave->user->save();
        }

        $leave->status = $request->status;
        $leave->save();
    });

    if ($leave->user) {
        $leave->user->notify(new SystemStatusNotification(
            'Leave request ' . $leave->status,
            'Your leave request for ' . $leave->days . ' day(s) has been ' . strtolower($leave->status) . ($leave->status === 'Approved' ? '. Remaining ' . strtolower($leave->type) . ' balance: ' . $this->getLeaveBalance($leave->user->fresh(), $leave->type) . ' day(s).' : '.'),
            strtolower($leave->status) === 'approved' ? 'success' : 'danger',
            route('leave-user')
        ));
    }

    // if (!empty($leave->user?->phone)) {
    //     $message = $leave->status === 'Approved'
    //         ? "Dear {$leave->user->name}, your leave request has been approved."
    //         : "Dear {$leave->user->name}, your leave request has been rejected.";

    //     $smsService->sendSms($leave->user->phone, $message);
    // }

    return back()->with('success', 'Leave status updated and SMS sent successfully.');
}


    public function update(Request $request, $id)
    {
        $leave = LeaveModel::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if (strtolower(trim($leave->status)) != 'pending') {
            return redirect()->back()->with('error', 'You cannot update this leave request.');
        }

        $request->validate([
            'date' => 'required|date',
            'days' => 'required|integer|min:1',
            'reason' => 'required|string',
            'type' => 'required|in:Casual Leave,Sick Leave,Paid Leave',
        ]);

        $balanceToUse = $this->getLeaveBalance(auth()->user(), $request->type);
        if ($leave->type === $request->type) {
            $balanceToUse += (float) $leave->days;
        }

        if ((float) $request->days > $balanceToUse) {
            return redirect()->back()->with('error', 'Not enough ' . strtolower($request->type) . ' balance. Available balance: ' . $balanceToUse . ' day(s).');
        }

        $leave->update([
            'from_date' => $request->date,
            'to_date' => $request->date,
            'days' => $request->days,
            'reason' => $request->reason,
            'type' => $request->type,
        ]);

        User::whereIn('role', ['Admin', 'HR', 'Manager'])
            ->where('id', '!=', auth()->id())
            ->get()
            ->each(function ($admin) use ($leave) {
                $admin->notify(new SystemStatusNotification(
                    'Leave request updated',
                    auth()->user()->name . ' updated a pending leave request.',
                    'leave',
                    route('leave-admin')
                ));
            });

        return redirect()->route('leave-user')->with('success', 'Leave updated successfully.');
    }

    public function destroy($id)
    {
        $leave = LeaveModel::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if (strtolower(trim($leave->status)) != 'pending') {
            return redirect()->back()->with('error', 'You cannot delete this leave request.');
        }

        $leave->delete();

        return redirect()->back()->with('success', 'Leave deleted successfully.');
    }
}
