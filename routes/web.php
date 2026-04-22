<?php
// use App\Models\User;
use Illuminate\Support\Facades\Route;
// use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserSettingsController;
use App\Http\Controllers\PasswordChangeController;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\NotificationController;
// use App\Services\SmsService;
// use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('index');
})->name('home');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::get('/logout', [AuthController::class, 'logoutViaGet'])->middleware('auth')->name('logout.get');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

Route::get('/manage-user', [UserController::class, 'index'])->name('manage-user');
Route::post('/manage-user', [UserController::class, 'store'])->name('manage-user.store');
Route::post('/manage-user/import', [UserController::class, 'import'])->name('manage-user.import');
Route::get('/manage-user/export', [UserController::class, 'export'])->name('manage-user.export');
Route::put('/manage-user/{id}', [UserController::class, 'update'])->name('manage-user.update');
Route::delete('/manage-user/{id}', [UserController::class, 'destroy'])->name('manage-user.delete');

Route::get('/departments', [DepartmentController::class, 'index'])->middleware('auth')->name('departments');
Route::post('/departments', [DepartmentController::class, 'store'])->middleware('auth')->name('departments.store');
Route::put('/departments/{id}', [DepartmentController::class, 'update'])->middleware('auth')->name('departments.update');
Route::delete('/departments/{id}', [DepartmentController::class, 'destroy'])->middleware('auth')->name('departments.destroy');

Route::get('/employee-details', [EmployeeController::class, 'index'])->middleware('auth')->name('employee-details');
Route::post('/employee-details', [EmployeeController::class, 'store'])->middleware('auth')->name('employee-details.store');
Route::delete('/employee-details/{id}', [EmployeeController::class, 'destroy'])->middleware('auth')->name('employee-details.destroy');


Route::middleware('auth')->group(function () {

    // Admin / HR / Manager Attendance Page
    Route::get('/attendance-admin', [AttendanceController::class, 'admin'])
        ->name('attendance-admin');

    // Employee Attendance Page
    Route::get('/attendance-user', [AttendanceController::class, 'user'])
        ->name('attendance-user');

    // Save Attendance
    Route::post('/attendance', [AttendanceController::class, 'store'])
        ->name('attendance.store');

    // Monthly Record Page
    Route::get('/attendance-record', [AttendanceController::class, 'record'])
        ->name('attendance.record');

});

Route::get('/leave-admin', [LeaveController::class, 'admin'])->middleware('auth')->name('leave-admin');
Route::get('/leave-user', [LeaveController::class, 'user'])->middleware('auth')->name('leave-user');
Route::post('/leave', [LeaveController::class, 'store'])->middleware('auth')->name('leave.store');
Route::put('/leave/status/{id}', [LeaveController::class, 'updateStatus'])->middleware('auth')->name('leave.update-status');
Route::put('/leave/{id}', [LeaveController::class, 'update'])->middleware('auth')->name('leave.update');
Route::delete('/leave/{id}', [LeaveController::class, 'destroy'])->middleware('auth')->name('leave.destroy');

Route::get('/salary-admin', [SalaryController::class, 'admin'])->middleware('auth')->name('salary-admin');
Route::get('/salary-user', [SalaryController::class, 'user'])->middleware('auth')->name('salary-user');
Route::get('/salary-preview', [SalaryController::class, 'preview'])->middleware('auth')->name('salary.preview');
Route::post('/salary', [SalaryController::class, 'store'])->middleware('auth')->name('salary.store');
Route::get('/salary-slip/{id}/download', [SalaryController::class, 'downloadSlip'])->name('salary-slip.download');
Route::get('/salary-slip/{id}', [SalaryController::class, 'slip'])->name('salary-slip');

Route::middleware('auth')->group(function () {
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::put('/tasks/{id}', [TaskController::class, 'update'])->name('tasks.update');
    Route::put('/tasks/{id}/submit', [TaskController::class, 'submit'])->name('tasks.submit');
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy'])->name('tasks.destroy');
});

Route::get('/user-settings', [UserSettingsController::class, 'index'])
    ->name('user-settings')
    ->middleware('auth');

Route::post('/user-settings', [UserSettingsController::class, 'update'])
    ->name('user-settings.update')
    ->middleware('auth');

Route::post('/notifications/{id}/read', [NotificationController::class, 'read'])
    ->middleware('auth')
    ->name('notifications.read');
Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])
    ->middleware('auth')
    ->name('notifications.read-all');
    
    Route::middleware('auth')->group(function () {
    Route::get('/change-password', [PasswordChangeController::class, 'showForm'])->name('password.change.form');
    Route::post('/change-password', [PasswordChangeController::class, 'updatePassword'])->name('password.change.update');
});


Route::get('/test-mail', function () {
    Mail::raw('Test email from Laravel EMS', function ($message) {
        $message->to('cse.220840131070@gmail.com')
                ->subject('Test Mail');
    });

    return 'Mail sent';
});
Route::get('/forgot-password', [PasswordController::class, 'forgotForm'])->name('forgot-password');
Route::post('/send-otp', [PasswordController::class, 'sendOtp'])->name('send-otp');
Route::get('/verify-otp', [PasswordController::class, 'verifyForm'])->name('verify-otp.form');
Route::post('/verify-otp', [PasswordController::class, 'verifyOtp'])->name('verify-otp');
Route::post('/reset-password', [PasswordController::class, 'updateForgotPassword'])->name('reset-password');
