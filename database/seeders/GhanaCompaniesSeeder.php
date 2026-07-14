<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GhanaCompaniesSeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            ['name' => 'MTN Ghana', 'location' => 'Accra', 'phone' => '0244300001', 'contact' => 'Corporate Affairs'],
            ['name' => 'Telecel Ghana', 'location' => 'Accra', 'phone' => '0501300002', 'contact' => 'HR Manager'],
            ['name' => 'AT Ghana', 'location' => 'Accra', 'phone' => '0277300003', 'contact' => 'People Operations'],
            ['name' => 'Ecobank Ghana', 'location' => 'Accra', 'phone' => '0302300004', 'contact' => 'HR Business Partner'],
            ['name' => 'GCB Bank', 'location' => 'Accra', 'phone' => '0302300005', 'contact' => 'Head of HR'],
            ['name' => 'Absa Bank Ghana', 'location' => 'Accra', 'phone' => '0302300006', 'contact' => 'Talent Lead'],
            ['name' => 'Stanbic Bank Ghana', 'location' => 'Accra', 'phone' => '0302300007', 'contact' => 'HR Director'],
            ['name' => 'Fidelity Bank Ghana', 'location' => 'Accra', 'phone' => '0302300008', 'contact' => 'People Partner'],
            ['name' => 'CalBank', 'location' => 'Accra', 'phone' => '0302300009', 'contact' => 'HR Manager'],
            ['name' => 'Access Bank Ghana', 'location' => 'Accra', 'phone' => '0302300010', 'contact' => 'HR Lead'],
            ['name' => 'Zenith Bank Ghana', 'location' => 'Accra', 'phone' => '0302300011', 'contact' => 'HR Manager'],
            ['name' => 'Republic Bank Ghana', 'location' => 'Accra', 'phone' => '0302300012', 'contact' => 'People Lead'],
            ['name' => 'Universal Merchant Bank', 'location' => 'Accra', 'phone' => '0302300013', 'contact' => 'HR Manager'],
            ['name' => 'Societe Generale Ghana', 'location' => 'Accra', 'phone' => '0302300014', 'contact' => 'HR Business Partner'],
            ['name' => 'Consolidated Bank Ghana', 'location' => 'Accra', 'phone' => '0302300015', 'contact' => 'Head of HR'],
            ['name' => 'Unilever Ghana', 'location' => 'Tema', 'phone' => '0303200016', 'contact' => 'HR Manager'],
            ['name' => 'Nestlé Ghana', 'location' => 'Tema', 'phone' => '0303200017', 'contact' => 'People & Culture'],
            ['name' => 'Guinness Ghana Breweries', 'location' => 'Accra', 'phone' => '0302200018', 'contact' => 'HR Lead'],
            ['name' => 'Accra Brewery Limited', 'location' => 'Accra', 'phone' => '0302200019', 'contact' => 'People Partner'],
            ['name' => 'Fan Milk Limited', 'location' => 'Accra', 'phone' => '0302200020', 'contact' => 'HR Manager'],
            ['name' => 'Coca-Cola Bottling Company of Ghana', 'location' => 'Accra', 'phone' => '0302200021', 'contact' => 'HR Director'],
            ['name' => 'PZ Cussons Ghana', 'location' => 'Tema', 'phone' => '0303200022', 'contact' => 'HR Manager'],
            ['name' => 'Kasapreko Company Limited', 'location' => 'Accra', 'phone' => '0302200023', 'contact' => 'People Lead'],
            ['name' => 'Melcom Ghana', 'location' => 'Accra', 'phone' => '0302200024', 'contact' => 'HR Manager'],
            ['name' => 'GOIL PLC', 'location' => 'Accra', 'phone' => '0302200025', 'contact' => 'Head of HR'],
            ['name' => 'TotalEnergies Marketing Ghana', 'location' => 'Accra', 'phone' => '0302200026', 'contact' => 'HR Business Partner'],
            ['name' => 'Vivo Energy Ghana', 'location' => 'Accra', 'phone' => '0302200027', 'contact' => 'People Operations'],
            ['name' => 'Ghana National Petroleum Corporation', 'location' => 'Accra', 'phone' => '0302200028', 'contact' => 'HR Manager'],
            ['name' => 'Tullow Ghana', 'location' => 'Accra', 'phone' => '0302200029', 'contact' => 'HR Lead'],
            ['name' => 'Kosmos Energy Ghana', 'location' => 'Accra', 'phone' => '0302200030', 'contact' => 'People Partner'],
            ['name' => 'Volta River Authority', 'location' => 'Accra', 'phone' => '0302200031', 'contact' => 'HR Director'],
            ['name' => 'GRIDCo', 'location' => 'Tema', 'phone' => '0303200032', 'contact' => 'HR Manager'],
            ['name' => 'Electricity Company of Ghana', 'location' => 'Accra', 'phone' => '0302200033', 'contact' => 'Head of HR'],
            ['name' => 'Ghana Water Company Limited', 'location' => 'Accra', 'phone' => '0302200034', 'contact' => 'HR Manager'],
            ['name' => 'Ghana Ports and Harbours Authority', 'location' => 'Tema', 'phone' => '0303200035', 'contact' => 'People Lead'],
            ['name' => 'Ghana Cocoa Board', 'location' => 'Accra', 'phone' => '0302200036', 'contact' => 'HR Manager'],
            ['name' => 'SSNIT', 'location' => 'Accra', 'phone' => '0302200037', 'contact' => 'Head of HR'],
            ['name' => 'Ghana Revenue Authority', 'location' => 'Accra', 'phone' => '0302200038', 'contact' => 'HR Director'],
            ['name' => 'National Health Insurance Authority', 'location' => 'Accra', 'phone' => '0302200039', 'contact' => 'People Partner'],
            ['name' => 'First National Bank Ghana', 'location' => 'Accra', 'phone' => '0302300040', 'contact' => 'HR Manager'],
            ['name' => 'Bank of Africa Ghana', 'location' => 'Accra', 'phone' => '0302300041', 'contact' => 'HR Lead'],
            ['name' => 'Prudential Bank Ghana', 'location' => 'Accra', 'phone' => '0302300042', 'contact' => 'HR Manager'],
            ['name' => 'Agricultural Development Bank', 'location' => 'Accra', 'phone' => '0302300043', 'contact' => 'Head of HR'],
            ['name' => 'Letshego Ghana', 'location' => 'Accra', 'phone' => '0302300044', 'contact' => 'People Lead'],
            ['name' => 'Enterprise Group', 'location' => 'Accra', 'phone' => '0302200045', 'contact' => 'HR Manager'],
            ['name' => 'SIC Insurance Company', 'location' => 'Accra', 'phone' => '0302200046', 'contact' => 'HR Business Partner'],
            ['name' => 'Hollard Insurance Ghana', 'location' => 'Accra', 'phone' => '0302200047', 'contact' => 'People Partner'],
            ['name' => 'Star Assurance Company', 'location' => 'Accra', 'phone' => '0302200048', 'contact' => 'HR Manager'],
            ['name' => 'Tobinco Pharmaceuticals', 'location' => 'Accra', 'phone' => '0302200049', 'contact' => 'HR Lead'],
            ['name' => 'Niche Cocoa Industry', 'location' => 'Tema', 'phone' => '0303200050', 'contact' => 'People Operations'],
        ];

        $departmentPool = [
            'Human Resources',
            'Finance',
            'Operations',
            'Information Technology',
            'Legal & Compliance',
            'Internal Audit',
            'Risk Management',
            'Corporate Strategy',
            'Administration',
            'Procurement',
            'Supply Chain',
            'Logistics',
            'Marketing',
            'Sales',
            'Customer Experience',
            'Public Relations',
            'Corporate Affairs',
            'Quality Assurance',
            'Research & Development',
            'Business Development',
            'Project Management',
            'Facilities Management',
            'Health, Safety & Environment',
            'Security',
            'Training & Development',
            'Payroll & Benefits',
            'Accounts Payable',
            'Accounts Receivable',
            'Treasury',
            'Credit Control',
            'Data Analytics',
            'Cybersecurity',
            'Network Operations',
            'Retail Operations',
            'Field Services',
            'Engineering',
            'Production',
            'Inventory Control',
            'Warehousing',
            'Fleet Management',
        ];

        foreach ($companies as $index => $data) {
            $n = $index + 1;
            $email = "company{$n}@gmail.com";

            $company = Company::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'location' => $data['location'].', Ghana',
                    'postal_address' => 'P.O. Box GH-'.str_pad((string) $n, 4, '0', STR_PAD_LEFT).', '.$data['location'],
                    'registration_number' => 'CG-GH-'.str_pad((string) $n, 5, '0', STR_PAD_LEFT),
                    'contact_person' => $data['contact'],
                    'is_active' => true,
                ]
            );

            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $data['name'].' Admin',
                    'password' => Hash::make('password'),
                    'role' => 'company_admin',
                    'company_id' => $company->id,
                    'phone' => $data['phone'],
                    'must_change_password' => false,
                ]
            );

            $hrPhone = '02'.str_pad((string) (4000000 + $n), 8, '0', STR_PAD_LEFT);

            User::updateOrCreate(
                ['email' => "hr{$n}@gmail.com"],
                [
                    'name' => $data['name'].' HR',
                    'password' => Hash::make('password'),
                    'role' => 'hr_staff',
                    'company_id' => $company->id,
                    'phone' => $hrPhone,
                    'must_change_password' => false,
                ]
            );

            // Deterministic 20–30 departments per company
            $pool = $departmentPool;
            mt_srand($company->id * 97 + $n);
            shuffle($pool);
            $count = 20 + ($n % 11); // 20–30
            $selected = array_slice($pool, 0, $count);
            sort($selected);

            foreach ($selected as $departmentName) {
                Department::firstOrCreate(
                    [
                        'company_id' => $company->id,
                        'name' => $departmentName,
                    ],
                    ['is_active' => true]
                );
            }

            Department::query()
                ->where('company_id', $company->id)
                ->whereNotIn('name', $selected)
                ->whereDoesntHave('enrollments')
                ->delete();
        }
    }
}
