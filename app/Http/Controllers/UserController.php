<?php

namespace App\Http\Controllers;

use App\Exports\UserExport;
use App\Imports\UserImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Mail\UserCredentialsMail;
use App\Notifications\SendSmsNotification;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function index()
    {
        if (!auth()->check() || strtolower(auth()->user()->role) != 'admin') {
            return redirect()->route('dashboard')->with('error', 'Access Denied');
        }

        $users = User::latest()->paginate(10);
        $userStats = [
            'total' => User::count(),
            'employees' => User::where('role', 'Employee')->count(),
            'leadership' => User::whereIn('role', ['Admin', 'HR', 'Manager'])->count(),
        ];

        return view('manage-user', compact('users', 'userStats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'role'  => 'required|string|in:Admin,HR,Manager,Employee',
        ]);

        $plainPassword = Str::random(10);
        $password = Hash::make($plainPassword);

        $phone = $this->normalizePhone($request->phone);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $phone,
            'role' => $request->role,
            'password' => $password,
            
        ]);

        // try {
        //     $user->notify(new SendSmsNotification(
        //         "Hello {$user->name}, your EMS account has been created successfully. Password: {$plainPassword}"
        //     ));
        // } catch (\Exception $e) {
        //     Log::error('SMS send failed: ' . $e->getMessage());
        // }

        if ($request->has('send_email')) {
            Mail::to($user->email)->send(new UserCredentialsMail($user, $plainPassword));
        }

        return redirect()->back()->with([
            'success' => 'User created successfully.',
            'temp_password' => $plainPassword,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'role' => 'required|string|in:Admin,HR,Manager,Employee',
        ]);

        $phone = $this->normalizePhone($request->phone);

        $user->update([
            'name' => $request->name,
            'phone' => $phone,
            'role' => $request->role,
        ]);

        return redirect()->back()->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', 'User deleted successfully.');
    }

// Import

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,txt|max:5120',
        ]);

        $import = new UserImport();

        Excel::import($import, $request->file('file'));

        if ($import->importedCount === 0) {
            return back()->with(
                'error',
                'No users were imported. Check the file headings and make sure the emails are new.'
            );
        }

        return back()->with(
            'success',
            "Users imported successfully. Added {$import->importedCount} user(s)" .
            ($import->skippedCount ? " and skipped {$import->skippedCount} row(s)." : '.')
        );
    }

    //Export

    public function export()
    {
        return Excel::download(new UserExport, 'users.xlsx');
    }

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (strlen($phone) === 10) {
            return '91' . $phone;
        }

        return $phone;
    }

}
