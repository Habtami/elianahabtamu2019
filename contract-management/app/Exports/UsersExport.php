<?php

namespace App\Exports;

use App\Helpers\ExcelExport;
use App\Models\User;

class UsersExport
{
    protected $users;

    public function __construct($users = null)
    {
        $this->users = $users ?: User::all();
    }

    public function download(string $filename = 'users_export.xlsx')
    {
        $excel = new ExcelExport(
            ['ID', 'Name', 'Email', 'Role', 'Department', 'Status', 'Created At'],
            'Users'
        );

        foreach ($this->users as $user) {
            $excel->addRow([
                $user->id,
                $user->name,
                $user->email,
                $user->getRoleDisplayName(),
                $user->department ? ucfirst(str_replace('_', ' ', $user->department)) : '',
                $user->status ? 'Active' : 'Inactive',
                $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : '',
            ]);
        }

        $excel->download($filename);
    }
}
