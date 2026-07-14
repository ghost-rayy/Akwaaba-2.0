<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Department;
use App\Models\Document;
use App\Models\University;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PendingReviewAndShortlistedSeeder extends Seeder
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

        // Ensure the shared posting letter PDF exists
        $sourcePdf = public_path('2.pdf');
        if (!is_file($sourcePdf)) {
            $this->command?->error('Missing public/2.pdf');
            return;
        }
        Storage::disk('public')->makeDirectory('documents/seeded');
        $letterPath = 'documents/seeded/posting_letter.pdf';
        if (!Storage::disk('public')->exists($letterPath)) {
            Storage::disk('public')->put($letterPath, file_get_contents($sourcePdf));
        }
        $letterSize = Storage::disk('public')->size($letterPath);

        // Ensure passport portrait pool exists
        $portraitPaths = $this->ensurePortraitPool();
        if ($portraitPaths === []) {
            $this->command?->error('Could not download passport portrait images.');
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
        $portraitCount = count($portraitPaths);

        $companies = Company::query()->orderBy('id')->get();

        foreach ($companies as $company) {
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

            if (!$adminId) {
                $this->command?->warn("Skipping {$company->name}: no company admin.");
                continue;
            }

            $this->seedStatus($company, $adminId, 'pending_review', $passwordHash, $now, $universities, $uniCount,
                $firstNames, $lastNames, $programmes, $cities, $cityKeys, $departmentIds,
                $letterPath, $letterSize, $portraitPaths, $portraitCount);
            $this->seedStatus($company, $adminId, 'shortlisted', $passwordHash, $now, $universities, $uniCount,
                $firstNames, $lastNames, $programmes, $cities, $cityKeys, $departmentIds,
                $letterPath, $letterSize, $portraitPaths, $portraitCount);
        }

        $this->command?->info('Done seeding pending_review and shortlisted personnel.');
    }

    private function seedStatus(
        Company $company, int $adminId, string $status, string $passwordHash, string $now,
        $universities, int $uniCount, array $firstNames, array $lastNames, array $programmes,
        array $cities, array $cityKeys, array $departmentIds,
        string $letterPath, ?int $letterSize, array $portraitPaths, int $portraitCount
    ): void {
        // Deterministic count: 20-50 based on company ID + status hash
        mt_srand(crc32($company->id . '_' . $status));
        $target = 20 + (mt_rand(0, 3000) % 31); // 20-50

        $countLabel = $status === 'pending_review' ? 'Pending Review' : 'Shortlisted';
        $this->command?->info("{$company->name}: seeding {$countLabel} {$target}...");

        $nextIndex = $this->nextPersonnelEmailIndex();
        $deptCount = count($departmentIds);
        $batchUsers = [];
        $batchMeta = [];

        $n = $company->id;

        for ($i = 1; $i <= $target; $i++) {
            $email = "personnel{$nextIndex}@gmail.com";
            $nextIndex++;
            $first = $firstNames[($n + $i) % count($firstNames)];
            $last = $lastNames[($i * 3 + $n) % count($lastNames)];
            $fullName = "{$first} {$last}";
            $nss = sprintf('NSS%02d%04d', $n, $i);
            $phone = '053' . str_pad((string)(($n - 1) * 1000 + $i), 7, '0', STR_PAD_LEFT);
            $city = $cityKeys[($i + $n) % count($cityKeys)];
            $region = $cities[$city];
            $uni = $universities[($i + $n) % $uniCount];
            $programme = $programmes[($i * 5 + $n) % count($programmes)];
            $departmentId = $departmentIds[($i - 1) % $deptCount];

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
                'nss_year' => (string) now()->year,
                'portrait_index' => ($i + $n) % $portraitCount,
            ];

            if (count($batchUsers) >= 100 || $i === $target) {
                $this->flushBatch($company, $adminId, $letterPath, $letterSize, $portraitPaths, $batchUsers, $batchMeta, $now);
                $batchUsers = [];
                $batchMeta = [];
                $this->command?->info("  … {$company->name} {$countLabel}: {$i}/{$target}");
            }
        }
    }

    private function flushBatch(
        Company $company, int $adminId, string $letterPath, ?int $letterSize,
        array $portraitPaths, array $batchUsers, array $batchMeta, string $now
    ): void {
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
        $documents = [];

        foreach ($batchMeta as $meta) {
            $userId = $userIds[$meta['email']] ?? null;
            if (!$userId) {
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
                'company_id' => $company->id,
                'department_id' => $meta['department_id'],
                'enrolled_by' => $adminId,
                'nss_number' => $meta['nss'],
                'nss_year' => $meta['nss_year'],
                'status' => $meta['status'],
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $photoPath = $portraitPaths[$meta['portrait_index']];

            $documents[] = [
                'user_id' => $userId,
                'company_id' => $company->id,
                'type' => 'posting_letter',
                'file_path' => $letterPath,
                'original_name' => 'posting_letter.pdf',
                'mime_type' => 'application/pdf',
                'size' => $letterSize,
                'is_verified' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $documents[] = [
                'user_id' => $userId,
                'company_id' => $company->id,
                'type' => 'passport',
                'file_path' => $photoPath,
                'original_name' => basename($photoPath),
                'mime_type' => 'image/jpeg',
                'size' => Storage::disk('public')->size($photoPath),
                'is_verified' => true,
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
        if ($documents !== []) {
            foreach (array_chunk($documents, 200) as $chunk) {
                DB::table('documents')->insert($chunk);
            }
        }
    }

    private function nextPersonnelEmailIndex(): int
    {
        $max = 0;
        foreach (User::query()->where('email', 'like', 'personnel%@gmail.com')->pluck('email') as $email) {
            if (preg_match('/^personnel(\d+)@gmail\.com$/', $email, $matches)) {
                $max = max($max, (int) $matches[1]);
            }
        }
        return $max + 1;
    }

    private function ensurePortraitPool(): array
    {
        $paths = [];
        $disk = Storage::disk('public');
        $disk->makeDirectory('documents/seeded/passports');

        foreach (['men' => 100, 'women' => 100] as $group => $total) {
            for ($i = 0; $i < $total; $i++) {
                $relative = "documents/seeded/passports/{$group}_{$i}.jpg";
                $full = $disk->path($relative);

                if (!is_file($full) || filesize($full) < 1000) {
                    $url = "https://randomuser.me/api/portraits/{$group}/{$i}.jpg";
                    try {
                        $response = \Illuminate\Support\Facades\Http::timeout(20)
                            ->withHeaders(['User-Agent' => 'AkwaabaSeeder/1.0'])
                            ->get($url);

                        if (!$response->successful() || strlen($response->body()) < 1000) {
                            continue;
                        }

                        $disk->put($relative, $response->body());
                    } catch (\Throwable $e) {
                        continue;
                    }
                }

                if (is_file($full) && filesize($full) >= 1000) {
                    $paths[] = $relative;
                }
            }
        }

        sort($paths);
        return $paths;
    }
}
