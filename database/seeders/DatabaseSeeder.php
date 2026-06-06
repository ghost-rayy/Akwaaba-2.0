<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@akwaaba.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'must_change_password' => true,
        ]);

        // Demo Company
        $company = Company::create([
            'name' => 'Tech Ghana Ltd',
            'email' => 'info@techghana.com',
            'phone' => '0244000001',
            'location' => 'Accra, Ghana',
            'postal_address' => 'P.O. Box 1234, Accra',
            'registration_number' => 'CG123456789',
            'contact_person' => 'John Doe',
        ]);

        // Company Admin
        User::create([
            'name' => 'Company Admin',
            'email' => 'company@akwaaba.com',
            'password' => Hash::make('password'),
            'role' => 'company_admin',
            'company_id' => $company->id,
            'must_change_password' => true,
        ]);

        // HR Staff
        User::create([
            'name' => 'HR Staff',
            'email' => 'hr@akwaaba.com',
            'password' => Hash::make('password'),
            'role' => 'hr_staff',
            'company_id' => $company->id,
            'must_change_password' => true,
        ]);

        // Sample NSS Personnel
        $personnel = User::create([
            'name' => 'NSS Personnel',
            'email' => 'personnel@akwaaba.com',
            'password' => Hash::make('password'),
            'role' => 'nss_personnel',
            'company_id' => $company->id,
            'nss_number' => 'NSS2026001',
            'must_change_password' => true,
            'form_step' => 0,
        ]);

        Enrollment::create([
            'user_id' => $personnel->id,
            'company_id' => $company->id,
            'enrolled_by' => 2,
            'nss_number' => 'NSS2026001',
            'status' => 'pending_forms',
        ]);
    }
}
