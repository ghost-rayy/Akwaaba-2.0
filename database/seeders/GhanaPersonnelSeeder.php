<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Department;
use App\Models\University;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GhanaPersonnelSeeder extends Seeder
{
    public function run(): void
    {
        @ini_set('memory_limit', '1024M');
        @set_time_limit(0);

        $passwordHash = Hash::make('password');
        $now = now()->format('Y-m-d H:i:s');
        $universities = University::query()->orderBy('name')->get(['name', 'region']);

        if ($universities->isEmpty()) {
            $this->command?->warn('No universities found. Run UniversitySeeder first.');

            return;
        }

        $firstNames = [
            'Kwame', 'Ama', 'Kofi', 'Akosua', 'Yaw', 'Abena', 'Kwesi', 'Adwoa', 'Kwabena', 'Afua',
            'Kojo', 'Serwaa', 'Fiifi', 'Efua', 'Nana', 'Akua', 'Kwadwo', 'Esi', 'Aba', 'Adjoa',
            'Michael', 'Grace', 'Daniel', 'Patience', 'Samuel', 'Comfort', 'Joseph', 'Blessing', 'Isaac', 'Ruth',
            'Emmanuel', 'Priscilla', 'Francis', 'Gifty', 'Prince', 'Mavis', 'Richmond', 'Sandra', 'Felix', 'Linda',
        ];

        $lastNames = [
            'Mensah', 'Asante', 'Owusu', 'Boateng', 'Appiah', 'Osei', 'Darko', 'Addo', 'Frimpong', 'Amoah',
            'Agyeman', 'Nyarko', 'Sarpong', 'Opoku', 'Adjei', 'Tetteh', 'Annan', 'Quaye', 'Lamptey', 'Ofori',
            'Bediako', 'Adu', 'Yeboah', 'Gyasi', 'Amponsah', 'Baah', 'Nkrumah', 'Danquah', 'Aikins', 'Kumi',
        ];

        $programmes = [
            'Computer Science', 'Information Technology', 'Accounting', 'Business Administration',
            'Economics', 'Marketing', 'Human Resource Management', 'Banking and Finance',
            'Electrical Engineering', 'Civil Engineering', 'Mechanical Engineering', 'Nursing',
            'Pharmacy', 'Agriculture', 'Communication Studies', 'Statistics', 'Law',
            'Procurement and Supply Chain', 'Public Administration', 'Geography',
        ];

        $cities = [
            'Accra' => 'Greater Accra',
            'Tema' => 'Greater Accra',
            'Kumasi' => 'Ashanti',
            'Tamale' => 'Northern',
            'Takoradi' => 'Western',
            'Cape Coast' => 'Central',
            'Koforidua' => 'Eastern',
            'Sunyani' => 'Bono',
            'Ho' => 'Volta',
            'Wa' => 'Upper West',
        ];
        $cityKeys = array_keys($cities);
        $uniCount = $universities->count();

        $companies = Company::query()
            ->where('email', 'like', 'company%@gmail.com')
            ->orderBy('email')
            ->get();

        foreach ($companies as $company) {
            if (! preg_match('/^company(\d+)@gmail\.com$/', $company->email, $matches)) {
                continue;
            }

            $n = (int) $matches[1];
            $target = 50 + (($n * 73 + 11) % 951); // 50–1000

            $departmentIds = Department::query()
                ->where('company_id', $company->id)
                ->where('is_active', true)
                ->pluck('id')
                ->all();

            if ($departmentIds === []) {
                $this->command?->warn("Skipping {$company->name}: no departments.");

                continue;
            }

            $adminId = User::query()
                ->where('company_id', $company->id)
                ->where('role', 'company_admin')
                ->value('id');

            if (! $adminId) {
                $this->command?->warn("Skipping {$company->name}: no company admin.");

                continue;
            }

            $existingCount = User::query()
                ->where('company_id', $company->id)
                ->where('role', 'nss_personnel')
                ->where('nss_number', 'like', sprintf('NSS%02d%%', $n))
                ->count();

            if ($existingCount >= $target) {
                $this->command?->info("{$company->name}: already has {$existingCount} seeded personnel (target {$target}).");

                continue;
            }

            $this->command?->info("{$company->name}: seeding personnel {$existingCount} → {$target}...");

            $nextPersonnelIndex = $this->nextPersonnelEmailIndex();
            $deptCount = count($departmentIds);
            $batchUsers = [];
            $batchMeta = [];

            for ($i = $existingCount + 1; $i <= $target; $i++) {
                $email = "personnel{$nextPersonnelIndex}@gmail.com";
                $nextPersonnelIndex++;
                $first = $firstNames[($n + $i) % count($firstNames)];
                $last = $lastNames[($i * 3 + $n) % count($lastNames)];
                $fullName = "{$first} {$last}";
                $nss = sprintf('NSS%02d%04d', $n, $i);
                $phone = '053'.str_pad((string) (($n - 1) * 1000 + $i), 7, '0', STR_PAD_LEFT);
                $city = $cityKeys[($i + $n) % count($cityKeys)];
                $region = $cities[$city];
                $uni = $universities[($i + $n) % $uniCount];
                $programme = $programmes[($i * 5 + $n) % count($programmes)];
                $departmentId = $departmentIds[($i - 1) % $deptCount];

                $roll = ($i + $n) % 10;
                $status = match (true) {
                    $roll < 7 => 'active',
                    $roll < 9 => 'validated',
                    default => 'completed',
                };

                $start = now()->subMonths(3 + ($i % 6))->startOfDay();
                $end = $start->copy()->addYear();
                $endorsedAt = $start->copy()->subWeeks(2);
                $validatedAt = $start->copy()->subWeek();

                $batchUsers[] = [
                    'name' => $fullName,
                    'email' => $email,
                    'password' => $passwordHash,
                    'role' => 'nss_personnel',
                    'company_id' => $company->id,
                    'phone' => $phone,
                    'nss_number' => $nss,
                    'must_change_password' => false,
                    'form_step' => 3,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $batchMeta[] = [
                    'email' => $email,
                    'full_name' => $fullName,
                    'nss' => $nss,
                    'phone' => $phone,
                    'city' => $city,
                    'region' => $region,
                    'university' => $uni->name,
                    'uni_region' => $uni->region ?: $region,
                    'programme' => $programme,
                    'department_id' => $departmentId,
                    'status' => $status,
                    'start_date' => $start->toDateString(),
                    'end_date' => $end->toDateString(),
                    'endorsement_date' => $endorsedAt->format('Y-m-d H:i:s'),
                    'validated_at' => $validatedAt->format('Y-m-d H:i:s'),
                    'nss_year' => (string) $start->year,
                ];

                if (count($batchUsers) >= 100 || $i === $target) {
                    $this->flushBatch($company->id, $adminId, $batchUsers, $batchMeta, $now);
                    $batchUsers = [];
                    $batchMeta = [];
                    $this->command?->info("  … {$company->name}: {$i}/{$target}");
                }
            }

            $this->command?->info("{$company->name}: done ({$target} personnel).");
        }
    }

    protected function nextPersonnelEmailIndex(): int
    {
        $max = 0;

        foreach (User::query()->where('email', 'like', 'personnel%@gmail.com')->pluck('email') as $email) {
            if (preg_match('/^personnel(\d+)@gmail\.com$/', $email, $matches)) {
                $max = max($max, (int) $matches[1]);
            }
        }

        return $max + 1;
    }

    /**
     * @param  list<array<string, mixed>>  $batchUsers
     * @param  list<array<string, mixed>>  $batchMeta
     */
    protected function flushBatch(int $companyId, int $adminId, array $batchUsers, array $batchMeta, string $now): void
    {
        if ($batchUsers === []) {
            return;
        }

        DB::table('users')->insert($batchUsers);

        $emails = array_column($batchUsers, 'email');
        $userIds = DB::table('users')
            ->whereIn('email', $emails)
            ->pluck('id', 'email');

        $personal = [];
        $education = [];
        $enrollments = [];

        foreach ($batchMeta as $meta) {
            $userId = $userIds[$meta['email']] ?? null;
            if (! $userId) {
                continue;
            }

            $personal[] = [
                'user_id' => $userId,
                'full_name' => $meta['full_name'],
                'nss_number' => $meta['nss'],
                'phone' => $meta['phone'],
                'email' => $meta['email'],
                'place_of_residence' => $meta['city'],
                'region_of_residence' => $meta['region'],
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $education[] = [
                'user_id' => $userId,
                'university' => $meta['university'],
                'city_of_school' => $meta['city'],
                'region_of_school' => $meta['uni_region'],
                'form_of_education' => 'Degree',
                'programme_of_study' => $meta['programme'],
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $enrollments[] = [
                'user_id' => $userId,
                'company_id' => $companyId,
                'department_id' => $meta['department_id'],
                'enrolled_by' => $adminId,
                'nss_number' => $meta['nss'],
                'nss_year' => $meta['nss_year'],
                'status' => $meta['status'],
                'start_date' => $meta['start_date'],
                'end_date' => $meta['end_date'],
                'endorsement_date' => $meta['endorsement_date'],
                'validated_at' => $meta['validated_at'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($personal !== []) {
            DB::table('personal_infos')->insert($personal);
        }
        if ($education !== []) {
            DB::table('education_infos')->insert($education);
        }
        if ($enrollments !== []) {
            DB::table('enrollments')->insert($enrollments);
        }
    }
}
