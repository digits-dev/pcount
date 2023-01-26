<?php

namespace App\Imports;

use App\Models\User;
use App\Models\UserPrivilege;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use CRUDBooster;
use Illuminate\Support\Facades\Hash;

class UserImport implements ToModel, WithHeadingRow, WithChunkReading
{
    public function model(array $row)
    {
        User::updateOrInsert([
            'email' => $row['email_address']
        ],[
            'name' => $row['name'],
            'user_name' => $row['user_name'],
            'email' => $row['email_address'],
            'photo' => 'uploads/1/2023-01/avatar.jpg',
            'id_cms_privileges' => UserPrivilege::withName($row['privilege'])->id,
            'password' => Hash::make('qwerty'),
            'status' => $row['status'],
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
