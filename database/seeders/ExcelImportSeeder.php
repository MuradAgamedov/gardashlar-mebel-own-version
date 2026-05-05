<?php

namespace Database\Seeders;

use App\Models\ExcelImport;
use Illuminate\Database\Seeder;

class ExcelImportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a default Excel import record
        ExcelImport::create([
            'file_url' => null, // No file uploaded initially
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
