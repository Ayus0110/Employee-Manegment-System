<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UserImport implements ToCollection, WithHeadingRow
{
    public int $importedCount = 0;
    public int $skippedCount = 0;

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $name = trim((string) ($this->value($row, ['name', 'full_name']) ?? ''));
            $email = trim((string) ($this->value($row, ['email', 'email_address']) ?? ''));
            $phone = trim((string) ($this->value($row, ['phone', 'phone_number', 'mobile']) ?? ''));
            $role = $this->normalizeRole($this->value($row, ['role', 'user_role']) ?? 'Employee');
            $password = (string) ($this->value($row, ['password', 'pass']) ?? '');

            if ($name === '' && $email === '' && $phone === '') {
                continue;
            }

            if ($name === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->skippedCount++;
                continue;
            }

            if (User::where('email', $email)->exists()) {
                $this->skippedCount++;
                continue;
            }

            User::create([
                'name' => $name,
                'email' => $email,
                'phone' => $this->normalizePhone($phone),
                'role' => $role,
                'password' => Hash::make($password !== '' ? $password : Str::random(10)),
                
            ]);

            $this->importedCount++;
        }
    }

    private function normalizePhone($phone): string
    {
        $phone = preg_replace('/\D/', '', (string) $phone);

        if (strlen($phone) === 10) {
            return '91' . $phone;
        }

        return $phone;
    }

    private function normalizeRole($role): string
    {
        return match (strtolower(trim((string) $role))) {
            'admin' => 'Admin',
            'hr' => 'HR',
            'manager' => 'Manager',
            default => 'Employee',
        };
    }

    private function value($row, array $keys): mixed
    {
        foreach ($keys as $key) {
            if (isset($row[$key])) {
                return $row[$key];
            }
        }

        return null;
    }
}
