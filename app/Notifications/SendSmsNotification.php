<?php

namespace App\Notifications;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\VonageMessage;

class SendSmsNotification extends Notification
{
    use Queueable;

    protected string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
     * Delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['vonage'];
    }

    /**
     * SMS content.
     */
    public function toVonage(object $notifiable): VonageMessage
    {
        return (new VonageMessage)
            ->content($this->message);
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

    // phone format convert to international
    $phone = preg_replace('/\D/', '', $request->phone);

    if (strlen($phone) === 10) {
        $phone = '91' . $phone;
    }

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $phone,
        'role' => $request->role,
        'password' => $password,
    ]);

    // Send SMS after user created
    try {
        $user->notify(new SendSmsNotification(
            "Hello {$user->name}, your EMS account has been created successfully. Password: {$plainPassword}"
        ));
    } catch (\Exception $e) {
        \Log::error('SMS send failed: ' . $e->getMessage());
    }

    // Send email if checkbox checked
    if ($request->has('send_email')) {
        Mail::to($user->email)->send(new UserCredentialsMail($user, $plainPassword));
    }

    return redirect()->back()->with([
        'success' => 'User created successfully.',
        'temp_password' => $plainPassword,
    ]);
}
}