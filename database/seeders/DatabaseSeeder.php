<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Department;
use App\Models\EducationInfo;
use App\Models\Enrollment;
use App\Models\PersonalInfo;
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

        // Departments
        $it = Department::create(['company_id' => $company->id, 'name' => 'IT']);
        $hr = Department::create(['company_id' => $company->id, 'name' => 'Human Resources']);
        $finance = Department::create(['company_id' => $company->id, 'name' => 'Finance']);
        $operations = Department::create(['company_id' => $company->id, 'name' => 'Operations']);

        // Company Admin
        $companyAdmin = User::create([
            'name' => 'Company Admin',
            'email' => 'company@akwaaba.com',
            'password' => Hash::make('password'),
            'role' => 'company_admin',
            'company_id' => $company->id,
            'must_change_password' => true,
        ]);

        // HR Staff
        $hrStaff = User::create([
            'name' => 'HR Staff',
            'email' => 'hr@akwaaba.com',
            'password' => Hash::make('password'),
            'role' => 'hr_staff',
            'company_id' => $company->id,
            'must_change_password' => true,
        ]);

        // Personnel 1 — fully onboarded, pending review
        $p1 = User::create([
            'name' => 'Yaa Asantewaa',
            'email' => 'personnel@akwaaba.com',
            'password' => Hash::make('password'),
            'role' => 'nss_personnel',
            'company_id' => $company->id,
            'nss_number' => 'NSS2026001',
            'phone' => '0244000011',
            'must_change_password' => true,
            'form_step' => 3,
        ]);

        $e1 = Enrollment::create([
            'user_id' => $p1->id,
            'company_id' => $company->id,
            'enrolled_by' => $companyAdmin->id,
            'nss_number' => 'NSS2026001',
            'nss_year' => '2025',
            'department_id' => $it->id,
            'status' => 'pending_review',
        ]);

        PersonalInfo::create([
            'user_id' => $p1->id,
            'full_name' => 'Yaa Asantewaa',
            'nss_number' => 'NSS2026001',
            'phone' => '0244000011',
            'email' => 'personnel@akwaaba.com',
            'place_of_residence' => 'Kumasi',
            'region_of_residence' => 'Ashanti',
        ]);

        EducationInfo::create([
            'user_id' => $p1->id,
            'university' => 'Kwame Nkrumah University of Science and Technology',
            'city_of_school' => 'Kumasi',
            'region_of_school' => 'Ashanti',
            'form_of_education' => 'Degree',
            'programme_of_study' => 'Computer Science',
        ]);

        // Personnel 2 — pending forms (just onboarded)
        $p2 = User::create([
            'name' => 'NSS Personnel',
            'email' => 'personnel2@akwaaba.com',
            'password' => Hash::make('password'),
            'role' => 'nss_personnel',
            'company_id' => $company->id,
            'nss_number' => 'NSS2026002',
            'must_change_password' => true,
            'form_step' => 0,
        ]);

        Enrollment::create([
            'user_id' => $p2->id,
            'company_id' => $company->id,
            'enrolled_by' => $companyAdmin->id,
            'nss_number' => 'NSS2026002',
            'nss_year' => '2025',
            'department_id' => $hr->id,
            'status' => 'pending_forms',
        ]);
    }
}
