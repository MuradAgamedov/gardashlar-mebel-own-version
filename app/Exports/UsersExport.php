<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return User::where('role', '!=', 'admin')
            ->select('id', 'name', 'surname', 'fin', 'email', 'phone', 'created_at')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Ad',
            'Soyad',
            'FIN',
            'Email',
            'Telefon',
            'Yaradılma tarixi',
        ];
    }
}
