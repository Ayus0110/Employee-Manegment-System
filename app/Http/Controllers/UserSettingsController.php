<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserSettingsController extends Controller
{
    //
     public function index(Request $request)
    {
        $authUser = Auth::user();
        $role = strtolower(trim($authUser->role ?? ''));
        $canViewAllProfiles = $role === 'admin';

        $user = $authUser;
        if ($canViewAllProfiles && $request->filled('user_id')) {
            $user = User::find($request->user_id) ?? $authUser;
        }

        $allUsers = $canViewAllProfiles
            ? User::orderByRaw("FIELD(role, 'Admin', 'HR', 'Manager', 'Employee')")
                ->orderBy('name')
                ->get()
            : collect();

        return view('user-settings', compact('user', 'allUsers', 'canViewAllProfiles'));
    }

//     public function update(Request $request)
//     {
//         $user = Auth::user();

//         $request->validate([
//             'name' => 'required',
//             'phone' => 'required',
//             'photo' => 'nullable|image',
//             'resume' => 'nullable|file',
//             'aadhaar' => 'nullable|file',
//         ]);

//         // update basic info
//         $user->name = $request->name;
//         $user->phone = $request->phone;

//         // file upload
//         if ($request->hasFile('photo')) {
//             $user->photo = $request->file('photo')->store('photos', 'public');
//         }

//         if ($request->hasFile('resume')) {
//             $user->resume = $request->file('resume')->store('resume', 'public');
//         }

//         if ($request->hasFile('aadhaar')) {
//             $user->aadhaar = $request->file('aadhaar')->store('aadhaar', 'public');
//         }

//         $user->save();

//         return back()->with('success', 'Profile updated');
//     }
//     public function edit()
// {
//     $user = auth()->user();
//     return view('user-settings', compact('user'));
// }

public function update(Request $request)
{
    $authUser = auth()->user();
    $role = strtolower(trim($authUser->role ?? ''));

    $user = $role === 'admin' && $request->filled('target_user_id')
        ? User::findOrFail($request->target_user_id)
        : $authUser;

    $request->validate([
        'name' => 'required|string|max:255',
        'phone' => 'nullable|string|max:20',
        'dob' => 'nullable|date',
        'address' => 'nullable|string',
        'department' => 'nullable|string|max:255',
        'designation' => 'nullable|string|max:255',
        'employee_id' => 'nullable|string|max:255',
        'basic_salary' => 'nullable|numeric',
        'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'resume' => 'nullable|mimes:pdf,doc,docx|max:4096',
        'aadhaar' => 'nullable|mimes:pdf,jpg,jpeg,png|max:4096',
    ]);

    $user->name = $request->name;
    $user->phone = $request->phone;
    $user->dob = $request->dob;
    $user->address = $request->address;
    $user->department = $request->department;
    $user->designation = $request->designation;
    $user->employee_id = $request->employee_id;
    $user->basic_salary = $request->basic_salary;

    if ($request->hasFile('photo')) {

    // delete old photo
    if ($user->photo && Storage::disk('public')->exists($user->photo)) {
        Storage::disk('public')->delete($user->photo);
    }

    // upload new photo
    $user->photo = $request->file('photo')->store('uploads/photos', 'public');
}

    if ($request->hasFile('resume')) {
    if ($user->resume && Storage::disk('public')->exists($user->resume)) {
        Storage::disk('public')->delete($user->resume);
    }

    $user->resume = $request->file('resume')->store('uploads/resumes', 'public');
}

    if ($request->hasFile('aadhaar')) {
    if ($user->aadhaar && Storage::disk('public')->exists($user->aadhaar)) {
        Storage::disk('public')->delete($user->aadhaar);
    }

    $user->aadhaar = $request->file('aadhaar')->store('uploads/aadhaar', 'public');
}

    $user->save();

    return back()->with('success', 'User details updated successfully.');
}
}
