<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Seed languages
        $this->call([
            AdminUserSeeder::class,
            LanguageSeeder::class,
            PolicyPagesSeeder::class,
            SuggestionSeeder::class,
            ProductSeeder::class,
            ProductColorSeeder::class,
            ProductPriceSeeder::class,
            BlogCategorySeeder::class,
            BlogPostSeeder::class, // Added this line
            CertificateSeeder::class,
            ServiceSeeder::class,
            ServiceStaticDataSeeder::class,
            IndividualProjectOrderSeeder::class,
            FaqSeeder::class,
            PhoneAndPhoneTypeSeeder::class,
            BecomeAPartnerSeeder::class,
            BecomeAInvestorSeeder::class,
            ReportSeeder::class,
            VacancySeeder::class,
            ProjectSeeder::class,
    
        ]);
    }
} 